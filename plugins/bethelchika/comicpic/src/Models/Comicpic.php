<?php

namespace BethelChika\ComicPic\Models;

use BethelChika\Laradmin\User;
use Illuminate\Database\Eloquent\Model;
use BethelChika\Laradmin\Media\Traits\Mediable;
use BethelChika\Laradmin\Media\Models\Media;
use Carbon\Carbon;

class Comicpic extends Model
{
    use Mediable;

    /**
    * The attributes that should be mutated to dates.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at',
        'published_at'
    ];
    /**
     * Define relationship with User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);

    }
    /**
     * Create a new comicpic object with associated Media and User objects 
     *
     * @param Media $media
     * @param User $user
     * @return ComicPic
     */
    public static function createWithMedia(Media $media, User $user)
    {
        $comicpic = new Comicpic;
        $comicpic->user_id = $user->id;
        $comicpic->save();

        $comicpic->medias()->save($media, ['tag' => 'comicpic']);
        $comicpic->makeImageThumb($media);

        return $comicpic; 
    }

    /**
     * Published this item it all riquired details are set
     *
     * @return boolean True when all is good. Returns false otherwise including when the item is already published
     */
    public function publish(){
        if(!$this->published_at and $this->title and $this->description){
            $this->published_at=Carbon::now();
            return $this->save();
        }else{
            return false;
        }
    }

    /**
     * Unpublished this item if has been published
     *
     * @return boolean True when all is good. Returns false otherwise including when the item is not published
     */
    public function unpublish(){
        if($this->published_at){
            $this->published_at=null;
            $this->unpublished_at=Carbon::now();
            return $this->save();
        }else{
            return false;
        }
    }

}
