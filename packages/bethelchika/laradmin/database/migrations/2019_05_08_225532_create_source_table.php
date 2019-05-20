<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            
            $table->increments('id');//TODO: create a froign key for this id in the main_permission table and cascabe on delete
            $table->string('name');
            $table->string('type_id');//this can be used for e.g url for 'URL' type of file path for 'File' type. ie. something that is unique among th same type.
            $table->string('type');
            $table->string('description');
            $table->string('user_id');
            $table->string('address')->nullable();
            $table->unique(['type', 'name']);
            $table->timestamps();
            // Potentia fields include:
            // hits=>couting access t the source
            // access_counts=>countings things like number of downloads
            // etc
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sources');
    }
}
