<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceFaults;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\Shifts;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceService extends Controller
{

    public static function getHomeData(){
        $data = Schedule::selectRaw('schedules.sch_id, b.time, a.emp_nam, d.atd_id, d.sts AS attend, e.fult_des')
                            ->leftjoin('employees AS a','a.emp_id','schedules.emp_id')
                            ->leftjoin('locations AS b','b.loc_id','schedules.loc_id')
                            ->leftjoin(DB::raw('(SELECT TIMEDIFF(a.strt_time ,a.end_time) AS time, a.shi_id FROM `shifts` AS a) AS b'),'b.shi_id','schedules.shi_id')
                            ->leftjoin('attendances AS d','d.sch_id','schedules.sch_id')
                            ->leftjoin('attendance_faults AS e','e.atd_id','d.atd_id')
                            ->get();
        return $data;
    }


    public static function inputData(Request $request){
        try {
        DB::beginTransaction();
        $the_file = $request->file()['file'];
        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->getActiveSheet();
        $row_limit    = $sheet->getHighestDataRow();
        $row_range    = range( 2, $row_limit );
        $startcount = 2;
        $data = array();
        foreach ( $row_range as $row ) {

            $object = (object) [
                'emp_nam' =>$sheet->getCell( 'A' . $row )->getValue(),
                'emp_add' => $sheet->getCell( 'B' . $row )->getValue(),
                'emp_cont' => $sheet->getCell( 'C' . $row )->getValue(),
                'emp_dob' => date_format(Date::excelToDateTimeObject($sheet->getCell( 'D' . $row )->getValue()),'Y-m-d'),
                'emp_status' => 1,
                'loc_nam'=>$sheet->getCell( 'E' . $row )->getValue(),
                'strt_time'=>date_format(Date::excelToDateTimeObject($sheet->getCell( 'F' . $row )->getValue()),'H:i:s'),
                'end_time'=>date_format(Date::excelToDateTimeObject($sheet->getCell( 'G' . $row )->getValue()),'H:i:s'),
                'she_dat'=>date_format(Date::excelToDateTimeObject($sheet->getCell( 'H' . $row )->getValue()),'Y-m-d'),
                'atd_dat'=>date_format(Date::excelToDateTimeObject($sheet->getCell( 'I' . $row )->getValue()),'Y-m-d'),
                'fult_des'=>$sheet->getCell( 'J' . $row )->getValue(),

            ];

            array_push($data,$object);
       
            $startcount++;
        }

        //Insert data to table
        foreach ($data as $val) {
            
            //Save Employee
            $emplexist = Employee::select('emp_id')->where('emp_nam',$val->emp_nam)->first();
           if (empty($emplexist)) {
            
                $empltabl = new Employee();
                $empltabl->emp_nam = $val->emp_nam;
                $empltabl->emp_add = $val->emp_add;
                $empltabl->emp_cont = $val->emp_cont;
                $empltabl->emp_dob = $val->emp_dob;
                $empltabl->emp_status = $val->emp_status;
                if(!$empltabl->save()){
                    throw new Exception('New Employee');
                }
                $emp_id = $empltabl->emp_id;
            }else{
                $emp_id = $emplexist->emp_id;
            }

            //Save Location
            $locationxist = Location::select('loc_id')->where('loc_nam',$val->loc_nam)->first();
            if (empty($locationxist)) {

                $locatontabl = new Location();
                $locatontabl->loc_nam = $val->loc_nam;
                $locatontabl->loc_status = 1;

                if(!$locatontabl->save()){
                    throw new Exception('New Location');
                }

                $loc_id = $locatontabl->loc_id;
            }else{
                $loc_id = $locationxist->loc_id;
            }

            //Save shifts
            $shiftexist = Shifts::select('shi_id')->where('strt_time',$val->strt_time)->where('end_time',$val->end_time)->first();
            if (empty($shiftexist)) {
                $shifttabl = new Shifts();
                $shifttabl->strt_time = $val->strt_time;
                $shifttabl->end_time = $val->end_time;
                $shifttabl->shi_status  = 1;
                if(!$shifttabl->save()){
                    throw new Exception('New Location');
                }
                $shi_id = $shifttabl->shi_id;
            }else{
                $shi_id = $shiftexist->shi_id;
            }
            //Save shedules
            $shedulTable = new Schedule();
            $shedulTable->emp_id = $emp_id;
            $shedulTable->loc_id = $loc_id;
            $shedulTable->shi_id = $shi_id;
            $shedulTable->she_dat = $val->she_dat;
            $shedulTable->shi_status = 1;
            if(!$shedulTable->save()){
                throw new Exception('New Shedule');
            }

            //Atendence save
            $attendtable = new Attendance();
            $attendtable->sch_id = $shedulTable->sch_id;
            $attendtable->emp_id = $emp_id;
            $attendtable->atd_dat = $val->atd_dat;
            $attendtable->sts = 1;
            if(!$attendtable->save()){
                throw new Exception('New Attendence');
            }

            //Attendence fault table
            $attfultTable = new AttendanceFaults();
            $attfultTable->atd_id = $attendtable->atd_id;
            $attfultTable->emp_id = $emp_id;
            $attfultTable->fult_des = $val-> fult_des;
            if(!$attfultTable->save()){
                throw new Exception('New Attendence Fault');
            }
        }
        DB::commit();

        $respons['sts'] = 1;

        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            $respons['sts'] = 0;

        }

        return $respons;
    }
}
