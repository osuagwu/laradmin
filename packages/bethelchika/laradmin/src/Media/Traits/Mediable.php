<?php
namespace BethelChika\Laradmin\Media\Traits;

use BethelChika\Laradmin\Media\Models\Media;
use BethelChika\Laradmin\Media\MediaManager;

trait Mediable
{






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
     * Get all of the Media for the Mediable.
     */
    public function medias()
    {
        return $this->morphToMany(Media::class, 'mediable')
            ->withPivot('title', 'description', 'tag')->withTimestamps();
    }


    public function makeImageThumb(Media $media)
    {
        $path = $media->manager()->makeImageThumb($media);
        //$this->medias()->update($media, ['thumb' => $path]);
    }

}