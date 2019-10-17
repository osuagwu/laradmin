<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->ipAddress('ip')->nullable();
            $table->float('latitude',10,8)->nullable();// because latitude ranges from -90 to +90
            $table->float('longitude',11,8)->nullable(); // because langitude ranges from -180 to +180 
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('languages')->nullable();
            $table->string('mobile_device')->nullable();
            $table->string('device_type')->nullable()->comment('{phone,tablet,desktop...}');
            $table->string('platform')->nullable();
            $table->string('platform_version')->nullable();
            $table->string('robot')->nullable();
            $table->integer('counts')->default(0)->comment('Number of times this attempt has been made');
            $table->float('rate',8,6)->default(0);
            $table->unsignedTinyInteger('is_success');
            $table->unsignedTinyInteger('is_verified')->default('0')->comment('Has this attempt been verified');//not implemented
            $table->unsignedTinyInteger('reverify')->default('0')->comment('Does this attempt need reverification');
            $table->integer('tries')->default('0')->comment('Number of attempts to verify or reverify');
            //$table->string('latest_session_id')->nullable()->comment('The last session id for the attempt');
            $table->json('credentials')->nullable();
            //$table->mediumText('http_headers')->nullable()->comment('Using medium text because I cannot tell how big headers can get');
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
        Schema::dropIfExists('login_attempts');
    }
}
