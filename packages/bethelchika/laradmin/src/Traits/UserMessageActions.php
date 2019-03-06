<?php
namespace BethelChika\Laradmin\Traits;

use Carbon\Carbon;
use BethelChika\Laradmin\User;
use BethelChika\Laradmin\UserMessage;

trait UserMessageActions
{
    /**
     * Delete messages owned by the specified user
     *
     * @param  User  $user
     * @return boolean
     */
    public static function destroyMessagesByUser(User $user){
        $messages=UserMessage::orwhere(['creator_user_id'=>$user->id, 'user_id'=>$user->id])->get();
        foreach($messages as $message){
            $message->deleteByUser($user);
        }
        return true;
    }


}