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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('disk', 32);
            $table->string('dir');//was directory
            $table->string('fn');
            $table->string('ext', 32);
            $table->string('mime_type', 128);
            //$table->string('aggregate_type', 32);
            $table->integer('size')->unsigned();
            $table->integer('width')->unsigned()->nullable();
            $table->integer('height')->unsigned()->nullable();
            $table->timestamps();
            
            $table->index(['disk', 'dir']);
            $table->unique(['disk', 'dir', 'fn', 'ext']);
            //$table->index('aggregate_type');
        });
        Schema::create('mediables', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->unsignedBigInteger('media_id');
            $table->string('mediable_type');
            $table->unsignedBigInteger('mediable_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('tag')->default('image');//'thumbnail', 'featured image', 'gallery' or 'download','hero','image' etc etc
            $table->integer('order_number')->default(0);//can be used to for ordering. Eg. a model could order all of its medias that have a particular tag. 
            $table->string('index_tags')->nullable();//used to insert search keywords
            $table->timestamps();
            
            
            $table->primary(['media_id', 'mediable_type', 'mediable_id', 'tag']);
            $table->index(['mediable_id', 'mediable_type']);
            $table->index('tag');
            

            //TODO:This prevents a media being used by the a mediable for the a particular tag more than once. But we need to check that there is a use case
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