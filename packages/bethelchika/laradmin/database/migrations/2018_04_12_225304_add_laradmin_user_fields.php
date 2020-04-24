<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLaradminUserFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();// Nullable is required by socialUser b/c email might not be supplied from social provider. Perhaps we can remove the nullable to say we must have email.
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();// Made nullable because of social users
        });

        Schema::table('users', function (Blueprint $table) {
            // TODO: A big issue here is that after we add these fields like 'settings' we 
            // cannot tell later if we in fact added it, making it difficult to know what 
            // to delete during migration refresh. A solution is to save a file contain
            // fields that we added ourselves so that we can know which to remove when 
            // required.

            //Essential
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'current_login_at')) {
                $table->timestamp('current_login_at')->nullable();
            }


            //Status
            if (!Schema::hasColumn('users', 'status')) {
                $table->tinyInteger('status')->default(-1);
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->tinyInteger('is_active')->default(1);
            }

            if (!Schema::hasColumn('users', 'hard_deleted_at')) {
                $table->timestamp('hard_deleted_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'self_delete_initiated_at')) {
                $table->timestamp('self_delete_initiated_at')->nullable();
            }

            if (!Schema::hasColumn('users', 'self_deactivated_at')) {
                $table->timestamp('self_deactivated_at')->nullable();
            }


            if (!Schema::hasColumn('users','avatar')){
                $table->string('avatar')->nullable();
            }

            // if (!Schema::hasColumn('users','cover_photo')){
            //     $table->string('cover_photo')->nullable();
            // }


            if (!Schema::hasColumn('users','local')){
                $table->string('local')->nullable();//
            }

            if (!Schema::hasColumn('users','timezone')){
                $table->string('timezone')->nullable();//
            }

            if (!Schema::hasColumn('users','xfactor')){
                $table->tinyInteger('xfactor')->default(0)->comment('When evaluates to true, then extra factor authen tication is enabled');//
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   $with_null=\Illuminate\Support\Facades\DB::table('users')->whereNull('password')->orWhereNull('email')->count();
        if (!$with_null) { //I.E Do not try to make these non-null if there are already users with null values, as it will fail
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->nullable(false)->change();
            });
            Schema::table('users', function (Blueprint $table) {
                $table->string('password')->nullable(false)->change();
            }
            );
        }


        //TODO: b/c its hard to tell if we actually added the fields(as they may have been added before)we will not do too many droppings here. Consequently we may not be performing clean uninstall of the fields we added
        Schema::table('users', function (Blueprint $table) {
            // Drop only one column inside this closure to avoid 'No such column error in sqlite'
            if (Schema::hasColumn('users', 'self_delete_initiated_at')) {
                $table->dropColumn('self_delete_initiated_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Drop only one column inside this closure to avoid 'No such column error in sqlite'
            if (Schema::hasColumn('users', 'self_deactivated_at')) {
                $table->dropColumn('self_deactivated_at');
            }
        });

    }
}
