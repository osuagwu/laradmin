<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfirmationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('confirmations', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');//TODO: column this is useless but row deletion is not working sqlite if it is removed. Todo will be to remember to reset this auto increment under set management from time to time to avoid the autoincrement overflowing 
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->integer('type_id')->nullable();
            $table->string('user_data')->nullable();
            $table->string('email_to')->nullable();
            $table->string('token')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users')
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
        Schema::dropIfExists('confirmations');
    }
}
