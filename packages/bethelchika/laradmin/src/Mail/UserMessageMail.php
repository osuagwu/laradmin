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
    public $message;
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
        $this->from($this->sender);
        $this->subject($this->userMessage->subject);
        $this->message=$this->userMessage->message;


        // Remove html except <br>
        $this->message= str_replace('<br>','\n',$this->message);
        $this->message=htmlspecialchars($this->message);
        $this->message= str_replace('\n','<br>',$this->message);
        

        return $this->markdown('laradmin::emails.user_message');
    }
}
