<?php

namespace BethelChika\Laradmin;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class SecurityAnswer extends Model
{

    protected $fillable=['security_question_id','answer','reminder'];
        
    function securityQuestion(){
        return $this->belongsTo('BethelChika\Laradmin\SecurityQuestion');
    }

    /**
     * Set the answer.
     *
     * @param  string  $value
     * @return void
     */
    public function setAnswerAttribute($value){
         //We first flaten the answer string and hash it 
         
         $this->attributes['answer'] = Hash::make($value);

    }

    /**
     * Verify a given answer
     *
     * @param  string  $answer
     * @return void
     */
    public function verify($answer){
        return Hash::check(self::cleanAnswer($answer),$this->answer);
        
   }

   /**
    * Make an answer ready
    *
    * @param string $answer
    * @return string
    */
   private static function cleanAnswer($answer){
       //We first flaten the answer string and hash it 
        $ans=strtolower($answer);
        $ans=preg_replace('/[^a-z\d]+/i', '', $ans);//Kepp only letters and numbers
        return preg_replace('/\s+/', '', $ans);//remove white spaces

   }

//    /**
//     * Undocumented function
//     *
//     * @param User $user
//     * @param string[] $answers
//     * @return void
//     */
//    public static function verifyAll(User $user,array $answers){
//        $answers=$user->securityAnswers;


//    }
  
}
