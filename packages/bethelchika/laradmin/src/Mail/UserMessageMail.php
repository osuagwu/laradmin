<?php

namespace BethelChika\Laradmin\Mail;

use BethelChika\Laradmin\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userMessage;
    public $sender;
    public $receiver;
    public $adminSender;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $sender,User $receiver,UserMessage $userMessage,$adminSender=null)
    {
        $this->userMessage=$userMessage;
        $this->sender=$sender;
        $this->receiver=$receiver;
        $this->adminSender=$adminSender;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
        $this->replyTo($this->sender);
        return $this->markdown('laradmin::emails.user_message');
    }
}
