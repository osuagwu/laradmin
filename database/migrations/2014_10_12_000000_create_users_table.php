<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            //Essential
            $table->bigIncrements('id');
            $table->string('name');//This is the same as nickname
            $table->string('email')->unique()->nullable();// Nullable is required by socialUser b/c email might not be supplied from social privider
            $table->string('password')->nullable();// Made nullable because of social users
            //$table->unsignedTinyInteger('is_password_auto_gen')->default(0);//
            $table->rememberToken();
            $table->timestamps();

            //$table->timestamp('last_login_at')->nullable();
            //$table->timestamp('current_login_at')->nullable();
            ////$table->timestamp('last_confirm_auth_at');//

            //More personal details
            $table->string('first_names')->nullable();
            $table->string('last_name')->nullable();
            $table->smallInteger('year_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('avatar')->nullable();
            $table->string('country')->nullable();
            $table->string('local')->nullable();//Can be the same as country
            $table->string('settings')->nullable();

            // //Status
            // $table->tinyInteger('status')->default(-1);
            // $table->tinyInteger('is_active')->default(0);
            // $table->timestamp('hard_deleted_at')->nullable();
            // $table->timestamp('self_delete_initiated_at')->nullable();
            // $table->timestamp('self_deactivated_at')->nullable();

            
            
            //extra
            $table->string('faith')->nullable();//May need to have this column inserted by an app who needs it
            
            
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
