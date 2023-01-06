<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceFaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_faults', function (Blueprint $table) {
            $table->integerIncrements('fult_id');
            $table->integer('atd_id')->unsigned()->index();
            $table->integer('emp_id')->unsigned()->index();
            $table->string('fult_des');

            $table->foreign('atd_id')->references('atd_id')->on('attendances')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('restrict')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_faults');
    }
}
