<?php

namespace BethelChika\Laradmin\Mail;

use BethelChika\Laradmin\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use BethelChika\Laradmin\UserMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsUserMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userMessage;
    public $senderName;
    public $senderEmail;
    public $receiver;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($senderEmail,$senderName,User $receiver,UserMessage $userMessage)
    {
        $this->userMessage=$userMessage;
        $this->senderName=$senderName;
        $this->senderEmail=$senderEmail;
        $this->receiver=$receiver;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
        $this->replyTo($this->senderEmail,$this->senderName);
        
        return $this->markdown('laradmin::emails.contact_us_user_message');
    }
}
