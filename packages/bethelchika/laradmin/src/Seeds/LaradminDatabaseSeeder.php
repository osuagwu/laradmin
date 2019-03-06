<?php
namespace BethelChika\Laradmin\Seeds;

use Ramsey\Uuid\Uuid;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Database\Seeder;
use BethelChika\Laradmin\Notifications\Notice;

class LaradminDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //echo "\nStarting seeding -Chika \n";


        //NOTE:  run 'composer dump-autoload' in cmd if class cannot be found
         $this->call(TablesSeeder::class);
         

         
         //Tell CP what has happened
        $user=(new User)->getSystemUser();
        $userMessage= new UserMessage;
        $userMessage->message="Hi CP,\n\n The database was reset";
        $userMessage->subject="System reset";
        $userMessage->channels=["database"];
        $userMessage->user_id=$user->id;//CP sends the message to self
        $userMessage->addToQuota($user);
        $userMessage->creator_user_id=$user->id;
        $userMessage->admin_creator_user_id=(new User)->getSuperId();
        $userMessage->id = Uuid::uuid4()->toString();
        $userMessage->secret=str_random(40);
        $userMessage->do_not_reply=true;
        $userMessage->save();

        $user->notify(new Notice('Database was reset!'));

    }
}
