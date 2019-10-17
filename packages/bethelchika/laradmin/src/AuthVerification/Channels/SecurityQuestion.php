<?php
namespace BethelChika\Laradmin\AuthVerification\Channels;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use BethelChika\Laradmin\AuthVerification\Contracts\Channel;
use BethelChika\Laradmin\AuthVerification\Models\AuthVerification;
use BethelChika\Laradmin\LoginAttempt;

class SecurityQuestion implements Channel{
/**
     * The unique tag
     *
     * @var string
     */
    private $tag='security_question';

    public function getTag(){
        return $this->tag;
    }

    public function getTitle(){
        return 'Security question';
    }

    public function getDescription(){
        return 'Answer security question';
    }

    /**
     * Verify
     *
     * @param LoginAttempt $attempt
     * @param array $code Answers The index of the array are the SecurityAnswer ids.
     * @return boolean|null The result is null if any answer in incorrect or not set
     */
    public function verify(LoginAttempt $attempt,array $code){
        $attempt=$attempt->tried();

        $answers=$attempt->user->securityAnswers;
        if(!$answers->count()){//The user has not answers
            return null;
        }

        // Check the answers
        for($i=0;$i<$answers->count();$i++){
            if(!$answers[$i]->verify($code[$answers[$i]->id])){
                return false;
            }

        }
        
        // Now verify
        return $attempt->verify($this);
        
    }
}