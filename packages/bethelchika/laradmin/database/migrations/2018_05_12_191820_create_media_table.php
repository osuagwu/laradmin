<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->unsigned();
            $table->string('disk', 32);
            $table->string('dir');//was directory
            $table->string('fn');
            $table->string('ext', 32);
            $table->string('mime_type', 128);
            //$table->string('aggregate_type', 32);
            $table->integer('size')->unsigned();
            $table->timestamps();
            
            $table->index(['disk', 'dir']);
            $table->unique(['disk', 'dir', 'fn', 'ext']);
            //$table->index('aggregate_type');
        });
        Schema::create('mediables', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('media_id')->unsigned();
            $table->string('mediable_type');
            $table->integer('mediable_id')->unsigned();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('tag')->defaullt('image');//'thumbnail', 'featured image', 'gallery' or 'download','hero','image'
            $table->string('index_tags')->nullable();//used to insert search keywords
            $table->timestamps();
            //$table->integer('order')->unsigned();
            
            $table->primary(['media_id', 'mediable_type', 'mediable_id', 'tag']);
            $table->index(['mediable_id', 'mediable_type']);
            $table->index('tag');
            //$table->index('order');

            //TODO:This prevents the a media beign used by the a mediable for the a particular tag more than once. But we need to check that there is a use case
            //$table->unique(['media_id', 'mediable_type', 'mediable_id', 'tag']); 

            $table->foreign('media_id')->references('id')->on('media')
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
        Schema::drop('mediables');
        Schema::drop('media');
    }
}