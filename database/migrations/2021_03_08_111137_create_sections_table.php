<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('Status');
            $table->bigInteger('Grade_id')->unsigned();
            $table->bigInteger('Class_id')->unsigned();
            $table->timestamps();
            $table->foreign('Grade_id')->references('id')->on('Grades')
                ->onDelete('cascade');
            $table->foreign('Class_id')->references('id')->on('Classrooms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
}
