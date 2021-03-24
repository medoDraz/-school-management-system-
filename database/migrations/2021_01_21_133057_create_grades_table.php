<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration {

	public function up()
	{
		Schema::create('grades', function(Blueprint $table) {
            $table->bigIncrements('id');
			$table->timestamps();
			$table->softDeletes();
			$table->longText('notes')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('grades');
	}
}
