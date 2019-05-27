<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     *
     * Command: php artisan make:migration --create=user_group_maps create_user_group_maps_table
     */
    public function up()
    {
        Schema::create('user_group_maps', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('user_group_id');
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
        Schema::dropIfExists('user_group_maps');
    }
}
