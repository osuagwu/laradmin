<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main_permissions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('source');//delete this
            $table->string('source_type');//new
            $table->string('source_id');//new
            $table->integer('user_id')->nullable();
            $table->integer('user_group_id')->nullable();
            $table->boolean('create');
            $table->boolean('read');
            $table->boolean('update');
            $table->boolean('delete');
            $table->timestamps();
            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');


            $table->foreign('user_group_id')
            ->references('id')->on('user_groups')
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
        Schema::dropIfExists('main_permissions');
    }
}
