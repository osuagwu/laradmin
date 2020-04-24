<?php

namespace BethelChika\Laradmin\Media\Models;

use Illuminate\Contracts\Filesystem\Factory as FileSystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Media extends Model
{
    /**
     * Relationship with other models. Retrieve all associated models of given class.
     * @param  string $class 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function models($class)
    {
        return $this->morphedByMany($class, 'mediable')->withPivot('title', 'description', 'tag','order_number');
    }

    /**
     * Alias of @see Media::storage()
     */
    public function fileSystem()
    {
        return $this->storage();
    }

    /**
     * Get the filesystem object for this media.
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    public function storage()
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
     * Properly delete a media object The media is only actually deleted if 
     * there is not model associated with it.
     *
     *  @return bool|null
     */
    public function delete()
    { 
        if ($this->countModels()<=1) {
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

        // Delete the rest of the sizes
        foreach($this->manager()->imageSizes() as $tag=>$size){
            
            $fullname=$this->getFullName($tag);
            
            if($this->storage()->exists($fullname)){
                $this->storage()->delete($fullname);

                //remove parent directory if it is empty
                $parent=dirname($fullname);
                if(empty($this->storage()->files($parent)) and empty($this->storage()->directories($parent))){
                    $this->storage()->deleteDirectory($parent);
                }

            }
        }

        //remove parent directory of thumbs if it is empty
        $parent=dirname($this->getThumbFullName() );
        if(empty($this->storage()->files($parent)) and empty($this->storage()->directories($parent))){
            $this->storage()->deleteDirectory($parent);

            // Then the main folder
            $parent=dirname($this->getFullName());
            if(empty($this->storage()->files($parent)) and empty($this->storage()->directories($parent))){
                $this->storage()->deleteDirectory($parent);
            }

        }

       
        
        return true;
    }

    /**
     * Get the size on disk of the media file by reading the actual file.
     * CAUTION: This method should be used for updating the Media::size. Read 
     * the property Media::size when serving requests as this method may be slower.
     *
     * @return int
     */
    public function readSize(){
        return $this->fileSystem()->size($this->getFullName());
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
        if(!is_null($this->width)){
            return $this->width;
        }else{
            $this->setDimensionFromSource();
        }
        return $this->width;
    }

     /**
     * Return the height of the image
     *
     * @return void
     */
    public function getHeight(){
        if(!is_null($this->height)){
            return $this->height;
        }else{
            $this->setDimensionFromSource();
        }
        return $this->height;
        
    }

    /**
     * Set the dimension of the media.
     *
     * @return void
     */
    private function setDimensionFromSource(){
        $dim=$this->manager()->imageDimensionFromSource($this);
        $this->width=$dim['w'];
        $this->height=$dim['h'];
        if($this->exists){
            $this->save();
        }
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
        return $this->dir;
        
    }

    /**
     * Returns the file name, i.e without extension
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fn;
        

    }

    /**
     * Returns the file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->ext;
        

    }

    /**
     * Returns the file full resource names [folder/../name.ext]. The name of this method has nothing to with the size_tag=>full
     *  @param $size_tag The image size tag 
     * @return string
     */
    public function getFullName($size_tag='full')
    {
        
        $subfolder=$this->manager()->sizeFolderName($this,$size_tag);
        if(is_null($subfolder)){
            return null;
        }
        elseif($subfolder){//not an empty string
            $subfolder='/'.$subfolder;
        }
        
        
        return rtrim(trim($this->getFolderName(),'/').$subfolder.'/'.$this->getFileName().'.'.$this->getExtension(),'.');//i.e we also strip the dot at the end incase  the file does not have extension
        
    }

    /**
     * The absolute uri of the file
     *
     * @param string $size_tag @see $this->getFileName
     * @return string
     */
    public function getAbsoluteFullName($size_tag='full'){
        return $this->storage()->path($this->getFullName($size_tag));
    }

  

    // TOCHECK: I comment this out because I cannot see the point of it.
    // public function getUrlAttribute(){
    //     return $this->url();
    // }

    /**
     * Undocumented function
     *
     * @param string|string[] $size_tags
     * @param boolean $type @see MediaManager::makeImageSize()
     * @return void
     */
    public function makeImageSizes($size_tags,$type='force'){
        if(!is_array($size_tags)){
            $size_tags=[$size_tags];
        }
        foreach($size_tags as $tag){
            $this->manager()->makeImageSize($this,$tag,$type);
        }

    }

    /**
     * Returns the media's url
     * @param string|array $size_tags The size tag. If it is array the first one that has an  existing image is used to compute the url.
     * @param boolean $relative Returns a relative link when true 
     * @return string|null
     */
    public function url($size_tags=['full'],$relative=true){
        if(!is_array($size_tags)){
            $size_tags=[$size_tags];
        }
        foreach ($size_tags as $tag) {
            $fullname=$this->getFullName($tag);
            if($this->exists(null,$fullname)) {
                if ($relative) {
                    return \Illuminate\Support\Facades\Storage::url($fullname);//Why this gives relative url...
                }
                return $this->storage()->url($fullname); ///... and this gives absolute url?
            }
            
        }
        return null;
    }

    /**
     * Checks if image of a given size exists for
     *
     * @param string $size_tag
     * @param string $filename if provided, the $size_tag will be ignored. The filename should be relative to the media's storage disk.
     * @return boolean
     */
    public function exists($size_tag='full',$filename=null){
        return $this->storage()->exists($filename??$this->getFullName($size_tag));
    }

    /**
     * Check if any image exists for the given size(s).
     *
     * @param array $size_tags
     * @return boolean
     */
    public function hasAny($size_tags=['full']){
        foreach ($size_tags as $tag) {
            if($this->exists($tag)){
                return true;
            }
        }
        return false;
    }

     /**
     * Returns the media's url for thumb
     *
     * @param boolean $relative Returns a relative link when true 
     * @return string
     */
    public function thumbUrl($relative=true){
        
        if($relative){
            return \Illuminate\Support\Facades\Storage::url($this->getThumbFullName());
        }
        return $this->storage()->url($this->getThumbFullName());
    }
    
}
