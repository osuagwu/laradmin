<?php
namespace BethelChika\Laradmin\Media\Traits;

use BethelChika\Laradmin\Media\Models\Media;
use BethelChika\Laradmin\Media\MediaManager;

trait Mediable
{





/**
 * Perform delete operations on the media before passing back to the mediable
 *
 *  @return bool|null
 */
    function delete()
    {
        $medias = $this->medias;

        

        foreach ($medias as $media) {
            $media->delete();
        }
        $this->medias()->detach();
        return parent::delete();
    }



    /**
     * Media relationship with the Mediable.
     *
     * @return void
     */
    public function medias()
    {
        return $this->morphToMany(Media::class, 'mediable')
            ->withPivot('title', 'description', 'tag','order_number')->withTimestamps();
    }


    /**
     * Creates thumb.
     * NOTE: This method is not so useful anymore b/c any image size can 
     * be created through registered image sizes. 
     *
     * @param Media $media
     * @return void
     */
    public function makeImageThumb(Media $media)
    {
        $path = $media->manager()->makeImageThumb($media);
        //$this->medias()->update($media, ['thumb' => $path]);
    }

}