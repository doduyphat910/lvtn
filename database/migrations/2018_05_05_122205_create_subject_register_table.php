<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_register', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_subjects');
            $table->string('class_room');
            $table->integer('id_teacher');
            $table->integer('qty_current');
            $table->integer('qty_max');
            $table->time('time_study_start');
            $table->time('time_study_end');
            $table->date('date_start');
            $table->date('date_end');
            $table->integer('id_time_register');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subject_register');
    }
}
