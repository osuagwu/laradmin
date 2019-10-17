<?php
namespace BethelChika\Laradmin\AuthVerification\Channels;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\AuthVerification\Contracts\Channel;
use BethelChika\Laradmin\AuthVerification\Models\AuthVerification;
use BethelChika\Laradmin\LoginAttempt;

class Password implements Channel{

    /**
     * The unique tag
     *
     * @var string
     */
    private $tag='password';

    public function getTag(){
        return $this->tag;
    }

    public function getTitle(){
        return 'Password';
    }

    public function getDescription(){
        return 'Enter your login password';
    }

  
    

    /**
     * Verify
     *
     * @param AuthVerification $auth_verification
     * @param string $code User password
     * @return boolean|null The result is null if password in incorrect or not set
     */
    public function verify(LoginAttempt $attempt,$code){
        $attempt=$attempt->tried();

        $current_password=$attempt->user->password;
        if(!$current_password){//The user has not set password
           // $attempt->tried();
            return null;
        }

        
        
        $re=Hash::check($code,$current_password);

        if(!$re){
            //$attempt->tried();
            return null;
        }
        
        return $attempt->verify($this);
        
        

        
    }

}