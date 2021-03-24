<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomsTable extends Migration {

	public function up()
	{
		Schema::create('classrooms', function(Blueprint $table) {
            $table->bigIncrements('id');
			$table->unsignedBigInteger('grade_id');
			$table->timestamps();
            $table->foreign('grade_id')->references('id')->on('grades')
                ->onDelete('cascade')
                ->onUpdate('cascade');
		});
	}

	public function down()
	{
		Schema::drop('classrooms');
	}
}
