<?php

namespace BethelChika\Laradmin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Notice extends Notification
{
    use Queueable;

    public $notice;
    public $action;
    public $actionURL;

    /**
     * Create a new notification instance.
     *
     * @param string $notice
     * @param string $action
     * @param string $actionURL
     * @return void
     */
    public function __construct($notice,$action=null,$actionURL=null)
    {
        $this->notice=$notice;
        $this->action=$action? $action:null;
        $this->actionURL=$actionURL? $actionURL:null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
        //return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if($this->action){
            return (new MailMessage)->markdown('laradmin::emails.notice',[
                'name'=>$notifiable->name,
                'notice'=>$this->notice,
                'action'=>$this->action,
                'actionURL'=>$this->actionURL

            ]);
                        
        }else{
            return (new MailMessage)->markdown('laradmin::emails.notice',[
                'name'=>$notifiable->name,
                'notice'=>$this->notice,

            ]);
        }
        
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($this->action){
            return [
                'name'=>$notifiable->name,
                'notice'=>$this->notice,
                'action'=>$this->action,
                'actionURL'=>$this->actionURL

            ];
                        
        }else{
            return [
                'css_class'=>'info',
                'name'=>'Notice',
                'notice'=>$this->notice,

            ];
        }
    }
}
