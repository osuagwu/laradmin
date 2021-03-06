<?php

namespace BethelChika\Laradmin;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use BethelChika\Laradmin\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use BethelChika\Laradmin\Mail\UserMessageMail;

use BethelChika\Laradmin\Notifications\Notice;
use BethelChika\Laradmin\Traits\UserMessageActions;

//use Illuminate\Notifications\Notifiable;
//use Illuminate\Foundation\Auth\User as Authenticatable;

class UserMessage extends Model
{
    use UserMessageActions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message', 'secret',
    ];
    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
       'created_at',
       'updated_at',
       'read_at',
       'deleted_by_sender_at',
       'deleted_by_receiver_at',
       
    ];

    /**
     * The "type" of the auto-incrementing ID..
     *
     * @var string
     */
    protected $keyType = 'uuid';

     /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'channels' => 'array',
    ];

    /**
     * The max allowed size of UserMessage quota for each user.
     *
     * @var int
     */
    public static $quotaMax =20000;


    /**
     * The max allowed size of UserMessage quota for admin.
     *
     * @var int
     */
    public static $adminQuotaMax=20000*1000;
    

    /**
     * Set the secret.
     *
     * @param  string  $value
     * @return void
     */
    public function setSecretAttribute($value)
    {
        $this->attributes['secret'] = bcrypt($value);
    } 
   

    /**
    * Relationship to User
    */
    function user(){
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    /**
    * Relationship to User
    */
    function sender(){
        return $this->belongsTo('BethelChika\Laradmin\User','creator_user_id');
    }

     /**
    * Relationship to User. The administrator who used CP to create this message
    */
    function adminSender(){
        return $this->belongsTo('BethelChika\Laradmin\User','admin_creator_user_id');
    }

    /**
     * Check if the supplied size is within quota set for admin
     * 
     * @param User $user
    * @return boolean
    */
    public function isWithinAdminQuota(User $user){
        
        return ($this->getQuotaSize() + $user->uer_message_quota)<=UserMessage::$adminQuotaMax;

        
    }

    /**
     * Check if the supplied size is within quota
     * 
     * @param User $user
    * @return boolean
    */
    public function isWithinUserQuota(User $user){
        
        return ($this->getQuotaSize() + $user->uer_message_quota)<=UserMessage::$quotaMax;

        
    }

    /**
     * Get message size
     * 
     * @param void
    * @return int
    */
    public function getQuotaSize(){
        return strlen($this->message); 
    }



    /**
     * Return unread messages for a specified user
     * 
     * @param User $user
    * @return \Illuminate\Support\Collection
    */
    public static function getUnReads(User $user){

        return $user->userMessages()->where(['read_at'=>null,'deleted_by_receiver_at'=>null])->get();
    }
    
    /**
     * Delete the thisuserMessage
     * 
     * @param User $deleter The user who wants to delete, who is either the sender or receiver
    * @return boolean
    */
    public function deleteByUser(User $deleter){
        $this->minusFromQuota($deleter);

        //If it is a self conversation, then delete straight away
        if($this->user_id==$this->creator_user_id){
            return $this->delete();
        }

        /*  The logged in user is the person trying to delete, check if 
        *   she is the sender or user(receiver) and mark delete accordingly
        */
        if($deleter->id==$this->user_id){
            $this->deleted_by_receiver_at=Carbon::now(); 
            
        }else{
            $this->deleted_by_sender_at=Carbon::now();
        
        }
        $this->save();

        //Delete if both sender and reciever has maked messaage as deleted
        if($this->deleted_by_user_at and $this->deleted_by_sender_at){
            return $this->delete();
        }
        return true;

        
    }

    /**
     * Increase the quota of the specifies user by the size of this data or if specified, by the given amount
     * 
     * @param User $user
     * @param int $amount
    * @return boolean
    */
    public function addToQuota(User $user,$amount=null){
            if($amount)$user->user_message_quota+=$amount;
            else $user->user_message_quota+=$this->getQuotaSize();
            if($user->exists){
                return $user->save();
            }
            return true;
    }

    /**
     * Decrease the quota of the specifies user by the size of this data or if specified, by the given amount
     * 
     * @param User $user
     * @param int $amount
    * @return boolean
    */
    public function minusFromQuota(User $user,$amount=null){
            if($amount) $user->user_message_quota-=$amount;
            else $user->user_message_quota-=$this->getQuotaSize();
            if ($user->exists) {
                return $user->save();
            }
            return false;
            
    }

    /**
     * Send a message
     * @param User $sender The message sender
     * @param User $reciever The message reciever
     * @param string $subject The subject of the message
     * @param string $message The message
     * @param array $channels {database,email}
     * @param boolean $do_not_reply Can this message be replied to
     * @param User $admin_creator_user Required when an CP is the sender
     * @return integer {
     *      -6 => Could not send because admin creator is required
     *      -5 => Email was sent but sender was out of qouta
     *      -4 => Email was not sent and sender was out of qouta
     *      -3 => Email was sent but reciever was out of qouta
     *      -2 => Email was not sent and reciever was out of qouta
     *      -1 => No channel worked
     *      1 =>all went well
     * }
     */
    public static function quickSend(User $sender,User $reciever,$subject,$message,$channels=['database'],$do_not_reply=false,?User $admin_creator_user=null){
        
        $userMessage= new UserMessage;
        $userMessage->message=$message;
        $userMessage->subject=$subject;
        $userMessage->channels=$channels;
        $userMessage->user_id=$reciever->id;//reciever

        $userMessage->creator_user_id=$sender->id;

        // If the sender is CP, we need set the admin person who is sending
        $system_user=(new User)->getSystemUser();
        if($sender->is($system_user)){
            if($admin_creator_user){
                $userMessage->admin_creator_user_id=$admin_creator_user->id;
            }else{
                return -6;
            }
        }
        

        $userMessage->id = Uuid::uuid4()->toString();
        $userMessage->secret=str_random(40);
        $userMessage->do_not_reply=$do_not_reply;
        

        $emailChennelWorked=false;
        if(in_array('email',$channels)){
            Mail::to($reciever->email)
            ->send(new UserMessageMail($sender,$reciever,$userMessage));
            $emailChennelWorked=true;
        }


        if(in_array('database',$channels)){

            if(!($userMessage->isWithinUserQuota($sender))){
                $sender->notify(new Notice('You user message quota limit is reached, you may not be able to receive further messages.'));
                if($emailChennelWorked){
                    return -5;
                }
                return -4;
            }

            if(!($userMessage->isWithinUserQuota($reciever))){
                $reciever->notify(new Notice('You user message quota limit is reached, you may not be able to receive further messages.'));
                if($emailChennelWorked){
                    return -3;
                }
                return -2;
            }

            // Note that we should set message content before this line else the quota will 
            // think the message is empty.        
            $userMessage->addToQuota($reciever);
            if(!($reciever->is($sender))){
                $userMessage->addToQuota($sender);
            }

            $userMessage->save();
            
        }else{
            if(!$emailChennelWorked){
                return -1;
            }
            
        }

        return 1;

    }

    /**
     * A Utility function to escape and process the content of message to make it safe etc.
     *
     * @return string The escaped content ready for display.
     */
    public function theContent(){
        $m= str_replace('<br>',"\n",$this->message);

        $m=htmlspecialchars($m);

        $url_pattern = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        $m= preg_replace($url_pattern, '<a href="http$2://$4" target="_blank" title="$0">$0 <small><i class="fas fa-external-link-alt"></i></small></a> ', $m);

        return nl2br($m); 
    }
}
