<?php
namespace BethelChika\Laradmin\AuthVerification\Models;

use Illuminate\Http\Request;
use BethelChika\Laradmin\User;
use Illuminate\Database\Eloquent\Model;
use BethelChika\Laradmin\AuthVerification\Contracts\Channel;
use BethelChika\Laradmin\AuthVerification\AuthVerificationManager;

class AuthVerification extends Model{
    protected $fillable=['user_id'];
    public static $manager=null;

    public function user()
    {
        return $this->belongsTo('BethelChika\Laradmin\User');
    }

    /**
     * Return the channels suitable for this verification
     *
     * @return array
     */
    public function getChannels(){
        return self::getManager()->getChannels($this->level);
    }

        /**
     * process the channel specified by the tag. 
     *
     * @param Request $request
     * @param string $tag A tag for a channel
     * @return mixed @see TODO: properly define
     */
    public function processChannel(Request $request,$tag){
        $this->channel=$tag;
        $this->save();
        
        $re=self::getManager()->getChannel($tag)->process($this,$request);
        if($re===true){
            $this->delete();
        }
        return $re;
    }

    /**
     * Can the given channel be used to verify this auth verification.
     *
     * @param Channel $channel
     * @return boolean
     */
    public function can(Channel $channel){
        return $this->canByTag($channel->getTag());        
    }

    /**
     * Can the given channel, spefied by tag, be used to verify thi auth verification.
     *
     * @param string $tag Channel unique tag
     * @return boolean
     */
    public function canByTag($tag){
        return self::getManager()->getChannelMaxLevel($tag)>=$this->level;
        
    }

    /**
     * Make verification by deleting row
     *
     * @param Channel $channel
     * @return boolean
     */
    public function verify(Channel $channel){
        if($this->can($channel)){
            return $this->delete();
        }

        $this->attempted();

        return false;
    }

    /**
     * Mark an attempt to verify
     *
     * @return void
     */
    public function attempted(){

        $this->attempts+=1;
        $this->save();

    }

    /**
     * Gets the Auth verification manager object
     *
     * @return void
     */
    public static function getManager(){
        if(!self::$manager){
            self::$manager=new AuthVerificationManager;
        }
        return self::$manager;
    }



   

}