<?php

namespace BethelChika\Laradmin\Media\Models;

use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    /**
     * Retrieve all associated models of given class.
     * @param  string $class 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function models($class)
    {
        return $this->morphedByMany($class, 'mediable')->withPivot('title', 'description', 'tag');
    }

    /**
     * Get the filesystem object for this media.
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function storage()
    {
        return app('filesystem')->disk($this->disk);
    }

    /**
     * Get the media manager.
     * @return \BethelChika\Laradmin\Media\MediaManager
     */
    public function manager()
    {
        return app('laradmin')->mediaManager;
    }

    /**
     * Properly delete a media object The media is only actually deleted if there is not model associated with it.
     *
     * @return void
     */
    public function delete()
    { 
        if ($this->countModels()==1) {
            $this->deleteFiles();
            return parent::delete();
        }
        return true;
    }

    /**
     * Delete associated files
     *
     * @return boolean
     */
    private function deleteFiles()
    {
        $this->storage()->delete($this->getFullName());
        $this->storage()->delete($this->getThumbFullName());
        
        return true;
    }

    /**
     * Return the thumbnail fullfile path
     *
     * @return void
     */
    public function getThumbFullName(){
        return $this->manager()->getThumbFullName($this);
    }

     /**
     * Return the width of the image
     *
     * @return void
     */
    public function getWidth(){
        return $this->manager()->getWidth($this);
    }

     /**
     * Return the height of the image
     *
     * @return void
     */
    public function getHeight(){
        return $this->manager()->getHeight($this);
    }

    /**
     * Count the number of models still associated with this media
     *
     * @return boolean
     */
    public function countModels()
    {
        return DB::table('mediables')->where('media_id', $this->id)->count();
    }

    // /**
    //  * Count the number of times the thumb for this media exist in the table
    //  * @param [type] $thumb_filename
    //  * @return void
    //  */
    // public function countThumbs($thumb_filename)
    // {
    //     // Get all other media with the same thumb
    //     $rows= DB::table('mediables')->where('thumb', $thumb_filename)->get();

    //     //For the returned thumb to be the same file, they must be in the same disk
    //     $c=0;
    //     foreach($rows as $row){
    //         $media=self::find($row['media_id']);
    //         if(!strcmp($media->disk,$this->disk)){
    //             $c++;
    //         }
    //     }
    //     return $c;
    // }
    
    
    /**
     * Returns the folder name
     *
     * @return string
     */
    public function getFolderName()
    {
        return $this->directory;
        
    }

    /**
     * Returns the file name, i.e without extension
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
        

    }

    /**
     * Returns the file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
        

    }

    /**
     * Returns the file full resource names [folder/../name.ext]
     *
     * @return string
     */
    public function getFullName()
    {
        return rtrim(trim($this->getFolderName(),'/').'/'.$this->getFileName().'.'.$this->getExtension(),'.');//i.e we also strip the dot at the end incase  the file does not have extension
        

    }

}
