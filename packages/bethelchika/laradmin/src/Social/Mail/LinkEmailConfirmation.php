<?php

namespace BethelChika\Laradmin\Social\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use BethelChika\Laradmin\User;

class LinkEmailConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $confirmationLink;
    public $linkEmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,$confirmationLink,$linkEmail)
    {
        $this->user=$user;
        $this->confirmationLink=$confirmationLink;
        $this->linkEmail=$linkEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('laradmin::user.social_user.emails.link_email_confirmation');
    }
}
