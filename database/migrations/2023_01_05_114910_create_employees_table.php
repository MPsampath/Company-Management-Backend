<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->integerIncrements('emp_id');
            $table->string('emp_nam',200);
            $table->string('emp_add');
            $table->string('emp_cont',20);
            $table->date('emp_dob');
            $table->boolean('emp_status')->default(1)->comment('Status(1-Active,0-Innactive)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
