<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('provider')->nullable();
            $table->string('social_name')->nullable();
            $table->string('social_email')->nullable();
            $table->string('social_id')->unique();
            $table->string('social_nickname')->nullable();
            $table->string('social_avatar')->nullable();
            $table->string('social_token')->nullable();
            $table->string('social_token_refresh')->nullable();
            $table->string('social_token_expires_in')->nullable();
            $table->string('social_token_secret')->nullable();
            $table->unsignedTinyInteger('login_enabled')->default(1);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('social_users');
    }
}
