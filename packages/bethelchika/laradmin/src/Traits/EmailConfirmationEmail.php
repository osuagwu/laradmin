<?php
namespace BethelChika\Laradmin\Traits;

use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\DB;
use BethelChika\Laradmin\Mail\UserConfirmation;

trait EmailConfirmationEmail
{
    /**
     * Send email confirmation email to the specified user
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
     function confirmEmailEmail(User $user){
 
        //$this->authorize('update', $user);         

        DB::table('confirmations')->where('user_id','=',$user->id)->where('type','=','email_confirmation')->delete();

        $token= str_random(40);
        $now=\Carbon\Carbon::now();
        DB::table('confirmations')->insert(['token'=>$token,'type'=>'email_confirmation','user_id'=>$user->id,'created_at'=>$now,'updated_at'=>$now]);
        $confirmationLink= route('email-confirmation',[$user->email,$token]);

        \Illuminate\Support\Facades\Mail::to($user->email)
        ->send(new UserConfirmation($user,$confirmationLink));
        
        return true;
    }
}