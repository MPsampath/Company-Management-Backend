<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->integerIncrements('sch_id');
            $table->integer('emp_id')->unsigned()->index();
            $table->smallInteger('loc_id')->unsigned()->index();
            $table->smallInteger('shi_id')->unsigned()->index();
            $table->date('she_dat');
            $table->boolean('shi_status',200)->default(1)->comment('Status(1-Active,0-Innactive)');

            $table->foreign('loc_id')->references('loc_id')->on('locations')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('shi_id')->references('shi_id')->on('shifts')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
