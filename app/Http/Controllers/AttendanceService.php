<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AttendanceService extends Controller
{
    public static function inputData(Request $request){
        try {
        $the_file = $request->file()['file'];
        $spreadsheet = IOFactory::load($the_file->getRealPath());

        $sheet        = $spreadsheet->getActiveSheet();
        $row_limit    = $sheet->getHighestDataRow();
        $row_range    = range( 2, $row_limit );
        $startcount = 2;
        $data = array();
        foreach ( $row_range as $row ) {
            if (!array_search($sheet->getCell( 'A' . $row )->getValue(),$data)) {

            $object = (object) [
                   
                'emp_nam' =>$sheet->getCell( 'A' . $row )->getValue(),
                'emp_add' => $sheet->getCell( 'B' . $row )->getValue(),
                'emp_cont' => $sheet->getCell( 'C' . $row )->getValue(),
                'emp_dob' => $sheet->getCell( 'D' . $row )->getValue(),
                'emp_status' => 1,
                'loc_nam'=>$sheet->getCell( 'E' . $row )->getValue(),
                'strt_time'=>$sheet->getCell( 'F' . $row )->getValue(),
                'end_time'=>$sheet->getCell( 'G' . $row )->getValue(),
                'she_dat'=>$sheet->getCell( 'H' . $row )->getValue(),
                'atd_dat'=>$sheet->getCell( 'I' . $row )->getValue(),
                'fult_des'=>$sheet->getCell( 'J' . $row )->getValue(),

            ];

            array_push($data,$object);
           
        }else{
        }
            $startcount++;
        }


    } catch (\Throwable $th) {
    dd($th);
    }
    }
}
