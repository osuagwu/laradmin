<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('secret')->unique();//TODO:Are we actually make sure that the secrets are unique as enforcing it here will make a message to fail if the secret is not unique
            $table->timestamp('deleted_by_sender_at')->nullable();
            $table->timestamp('deleted_by_receiver_at')->nullable();
            $table->json('channels');//$table->string('channels');//FIXME:Needs to be Json i.e=> $table->json('channels');
            $table->unsignedBigInteger('user_id');
            $table->string('reply_address')->nullable();//usefull for guest contacting us
            $table->unsignedBigInteger('creator_user_id');
            $table->unsignedBigInteger('admin_creator_user_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('message');
            $table->string('subject')->nullable();
            $table->boolean('do_not_reply')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

            $table->foreign('creator_user_id')
            ->references('id')->on('users')
            ->onDelete('cascade');

            
        });

        //Quota to users: NOTE that we can also add this directly on users table, but keeping it here means we keep related stuff together
        Schema::table('users', function (Blueprint $table) {
            $table->integer('user_message_quota')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_message_quota']);
        });
        Schema::dropIfExists('user_messages');
    }
}
