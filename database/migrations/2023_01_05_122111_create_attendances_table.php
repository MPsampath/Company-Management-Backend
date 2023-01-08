<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->integerIncrements('atd_id');
            $table->integer('sch_id')->unsigned()->index();
            $table->integer('emp_id')->unsigned()->index();
            $table->date('atd_dat');
            $table->boolean('sts')->default(1)->comment('Status(1-Active,0-Innactive)');


            $table->foreign('sch_id')->references('sch_id')->on('schedules')->onDelete('restrict')->onUpdate('cascade');
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
        Schema::dropIfExists('attendances');
    }
}
