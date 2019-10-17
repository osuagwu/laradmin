<?php
namespace BethelChika\Laradmin\Seeds;

use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserGroup;
use BethelChika\Laradmin\UserGroupMap;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use BethelChika\Laradmin\SecurityQuestion;

class TablesSeeder extends Seeder {

    private function seedUserGroups(){
        DB::table('user_groups')->delete();
        $userGroups=[];
        
                // #1 Banned group
                $userGroups['banned']=UserGroup::create(['name' => 'Banned',
                    'description'=>'Restricted group'
                    
                ]);
        
               // #2 Admin group
               $userGroups['admin_users']=UserGroup::create(['name' => 'Admin users',
                    'description'=>'Administrators group'
               
                ]);
        
                // #3 Power users group
                $userGroups['power_users']=UserGroup::create(['name' => 'Power users',
                    'description'=>'Should be set to have most powers but not permission changing'
                
                ]);
        
                // #4 Editors group
                $userGroups['editors']=UserGroup::create(['name' => 'Editors',
                    'description'=>'Powers to CP messages, pages, posts and all other front end contents'
        
                ]);
        
                // #5 Normal user group
                $userGroups['users']=UserGroup::create(['name' => 'Users',
                    'description'=>'Normal user group'
            
                ]);

                $this->command->info('User_groups table seeded!');

                return $userGroups;
                            
    }
    private function seedUsers(){
        //DB::table('users')->delete(); // Open this to wipe table first
        $users=[];
            // #1 Control Panel (system)
            $users['cp']=User::create(['email' => 'cp@localhost',
                        'name'=>'Control Panel',
                        'is_active'=>1,
                        'password'=>bcrypt(str_random(40)),
            ]);

            // #2 Super user
            $users['super']=User::create(['email' => 'super@localhost',
                        'name'=>'Super',
                        'is_active'=>1,
                        'password'=>bcrypt('super'),
                        ]);
            $users['super']->status=1;
            $users['super']->save();

            // #3 admin User
            $users['admin']=User::create(['email' => 'admin@localhost',
                        'name'=>'Administrator',
                        'is_active'=>1,
                        'password'=>bcrypt('admin'),
                        ]);

            // #4 Power user a
            $users['power']=User::create(['email' => 'powera@localhost',
                        'name'=>'Power User a',
                        'is_active'=>0,
                        'password'=>bcrypt('powera'),
            ]);

           

            // #5 Guest user, any person who visits the site without login in
            $users['guest']=User::create(['email' => 'guest'.str_random(5).'@localhost',
                        'name'=>'Guest user',
                        'is_active'=>1,
                        'password'=>bcrypt(str_random(40)),
            ]);


            // #6 dummy user a
            $users['usera']=User::create(['email' => 'usera@localhost',
                        'name'=>'Dummy User a',
                        'is_active'=>0,
                        'password'=>bcrypt('usera'),
                        ]);

            // #7 dummy user b
            $users['userb']=User::create(['email' => 'userb@localhost',
                        'name'=>'Dummy User b',
                        'is_active'=>0,
                        'password'=>bcrypt('userb'),
                        ]);

            $this->command->info('User table seeded!');
        
        return $users;
    }

    private function seedUserGroupMaps($users,$userGroups)
    {
        DB::table('user_group_maps')->delete();


        // #1 Map admin user
        UserGroupMap::create(['user_id' => $users['admin']->id,
            'user_group_id'=>$userGroups['admin_users']->id
        ]);

        // #2 Power user
        UserGroupMap::create(['user_id' => $users['power']->id,
            'user_group_id'=>$userGroups['power_users']->id
        ]);

        

        // #3 Map  dummy user a
        UserGroupMap::create(['user_id' => $users['usera']->id,
            'user_group_id'=>$userGroups['users']->id
        ]);
        
        // #4 Map dummy user b
        UserGroupMap::create(['user_id' => $users['userb']->id,
            'user_group_id'=>$userGroups['users']->id
        ]);

                    
        $this->command->info('User_group_maps table seeded!');
    }



    private function seedSecurityQuestions()
    {
        DB::table('security_questions')->delete();

        SecurityQuestion::create(['question' => 'What is the name of your favourite musical artist?'
        ]);

        SecurityQuestion::create(['question' => 'What is the name of your favourite teacher?'
        ]);

        SecurityQuestion::create(['question' => 'What is the name of your pet?'
        ]);

        SecurityQuestion::create(['question' => 'Where is your home town?'
        ]);

        $this->command->info('security_questions table seeded!');
    }




    public function run()
    {
        $userGroups=$this->seedUserGroups();
        $users=$this->seedUsers();
        $this->seedUserGroupMaps($users,$userGroups);

        $this->seedSecurityQuestions();

       

        // Check that values in the default groups and users are as aspected and tell the correct value to be put into env file 
        $this->command->info('-');
        $this->command->info('------------- Insert (if any) this in env file, unless you want other values -------------');
        if($userGroups['banned']->id !=config('laradmin.banned_usergroup_id',1)){
            $this->command->info(' LARADMIN_BANNED_USERGROUP_ID='.$userGroups['banned']->id);
        }
        if($userGroups['admin_users']->id !=config('laradmin.admin_usergroup_id',2)){
            $this->command->info(' LARADMIN_ADMIN_USERGROUP_ID='.$userGroups['admin_users']->id);
        }
        if($users['cp']->id !=config('laradmin.cp_id',1)){
            $this->command->info(' LARADMIN_CP_ID='.$users['cp']->id);
        }
        if($users['super']->id !=config('laradmin.super_id',2)){
            $this->command->info(' LARADMIN_SUPER_ID='.$users['super']->id);
        }        
        if($users['guest']->id !=config('laradmin.guest_id',5)){// The 
            $this->command->info(' LARADMIN_GUEST_ID='.$users['guest']->id);
        }

        $this->command->info('------------- End insert for env file -------------');
        $this->command->info('-');
        
    }
    

}