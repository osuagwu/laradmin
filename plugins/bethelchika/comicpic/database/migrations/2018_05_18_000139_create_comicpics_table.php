<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComicpicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comicpics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->unsigned();
            //$table->bigInteger('mediable_id')->unsigned();
            $table->string('provider')->default('local');// {'local', 'facebook','google','s3'}
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('twitter_screen_names')->nullable();
            $table->string('twitter_via')->nullable();
            $table->string('lang')->default('en');//language
            $table->timestamp('published_at')->nullable();
            $table->timestamp('unpublished_at')->nullable();
            $table->timestamps();
        });
    } 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comicpics');
    }
}
