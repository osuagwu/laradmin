<?php

namespace BethelChika\Laradmin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use BethelChika\Laradmin\User;

class UserConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $confirmationLink;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,$confirmationLink)
    {
        $this->user=$user;
        $this->confirmationLink=$confirmationLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('laradmin::emails.user_confirmation');
    }
}
