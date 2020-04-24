<?php

namespace BethelChika\Laradmin\AuthVerification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use BethelChika\Laradmin\User;

class EmailChannel extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,$code)
    {
        $this->user=$user;
        $this->code=$code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(config('app.name'). ' user verification email');
        return $this->markdown('laradmin::user.auth_verification.channels.email.mail');
    }
}
