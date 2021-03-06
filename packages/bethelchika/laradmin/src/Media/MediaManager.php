<?php
namespace BethelChika\Laradmin\Media;

use BethelChika\Laradmin\Media\Exceptions\ReservedImageSizeException;
use BethelChika\Laradmin\Media\Models\Media;
use BethelChika\Laradmin\Meta\Exceptions\MetaSaveContextException;
use BethelChika\Laradmin\Meta\Option;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Image;

/**
 * A class for managing the media model
 */
class MediaManager
{
    //private $add_image_size( 'custom-size', 220, 180 );
    /**
     * In addition to thumbnail, this array holds sizes that should be created from any given image with adequate dimension. 
     *  The sizes are in pixels
     * @var array
     */
    private $imageSizes=[ 
        //Reserved
        'full'=>['w'=>null,'h'=>null],//reserved tag which refers to the main uploaded image.
        
        //None reserved: so these can be overridden
        '_cover_photo_sm_'=>['w'=>780,'h'=>260],//Allows us to create smaller version of cover photo for mobiles.
        
        
    ];
    /**
     * Stores the image sizes retrieved from options table.
     *
     * @var array
     */
    private $imageOptionSizes=[];

    /**
     * The name of the images sizes in the options table
     *
     * @var string
     */
    private $imageSizesOptionName='__media_image_sizes';

    //*********************************************** */
    // TODO: Note that the thumb* referred  below predates self::$imageSizes  
    // and may later be removed completely as arbitrary sizes can be 
    // implemented with self::$imageSizes.
    //************************************************** */

    /**
     * The default width of thumbnails in pixels
     * 
     * 
     * @var integer
     */
    private $thumbWidth = 400;

    /**
     * The default height of thumbnails in pixels
     * 
     * @var integer
     */
    private $thumbHeight = 300;

    /**
     * The folder name relative to a Media's folder where thumbnails are kept
     *
     * @var string
     */
    private $thumbRelativeFolder = '/thumbs/';

    /**
     * The thumbnail file extension specifying the file type
     *
     * @var string
     */
    private $thumbFileExtension = 'png';

    /** 
     * @var \Illuminate\Contracts\Filesystem\Factory 
     */
    protected $filesystem;

  //protected $managerModel;

    /**
     * The max aspect ratio difference between two dimensions for them to be 
     * considered to have the same aspect ratio. 
     *
     * @var float
     */
    protected $aspectRatioTolerance=0.3;

    /**
     * Construct a new media
     *
     * 
     */
    public function __construct()
    {
        
    }


    /**
     * Returns image image manager
     *
     * @return Intervention\Image\ImageManager
     */
    public function getImageManager()
    {
        return app('image');
    }

    /**
     * Registers an image size
     *
     * @param string $tag
     * @param integer $width
     * @param integer $height
     * @return void
     * @throws MetaSaveContextException|ReservedImageSizeException
     */
    public function registerImageSize($tag,$width,$height,$save=false){
        if(!strcmp($tag,'full') or !strcmp($tag,'thumbs')){
            throw new ReservedImageSizeException('The tags, full and thumbs, are reserved');
        }
        if($save){
            if(app()->runningInConsole()){
                $sizes=Option::get($this->imageSizesOptionName);
                $sizes=unserialize($sizes);
                $sizes[$tag]=['w'=>$width,'h'=>$height];
                Option::add($this->imageSizesOptionName,serialize($sizes));
            }else{
                throw new MetaSaveContextException();
            }
            
        }
        
        $this->imageSizes[$tag]=['w'=>$width,'h'=>$height];
    }

     /**
     * Returns the available image sizes including those added to the options table
     *
     * @return array
     */
    public function imageSizes(){
        
        static $has_loaded_options=false;//$this->$hasLoadedImageOptionSizes;

        if(!$has_loaded_options){
            $this->imageOptionSizes=$this->imageOptionSizes()??[];
            $has_loaded_options=true;
        }
        return  array_merge($this->imageSizes,$this->imageOptionSizes);
     }

     /**
      * Return the Image sizes added to options table
      *
      * @return mixed
      */
     private function imageOptionSizes(){
        $meta=Option::get($this->imageSizesOptionName);
        if($meta){
            return unserialize($meta);
        }
        return null;
     }


    /**
     * Delete a media
     *
     * @param Media $media
     * @return boolean
     */
    public function delete(Media $media)
    {
      return $media->delete();

    }

    /**
     * Create a Media object
     *
     * @param string $source The file source - i.e url/location
     * @param string $destination The folder in which to save file
     * @param string $name The name to used to save the file. This excludes the directory name, {default:AUTO GENERATED}
     * @param string $disk {default:local}
     * @param string $visibility {default:private}
     * @return Media 
     */
    public function fromSource($source, $destination, $name = null, $disk = null, $visibility = null, Model $user = null)
    {
    //$im=$this->getImageManager();
    
      //$name='change';
        $disk = $disk ? $disk : 'local';
        $visibility = $visibility ? $visibility : 'private';
        $user_id = $user ? $user->id : 1; //TODO: Need to change the default to CP_ID

        $media = new Media;
        $media->disk=$disk;

        $destination = $this->sanitizePath($destination);
        

        if ($name) {
            $name = $this->sanitizeFileName($name);
            $parts = explode('.', $name);
            $extension = array_pop($parts);
            $name_only=implode('.',$parts);
        // Make filename unique based on database
            $found = true;
            $c = 0;
            $name_only_temp=$name_only;
            while ($found) {
                $found = Media::where('dir',$destination)
                ->where('disk',$disk)// Added on  26/05/2019
                ->where('fn', $name_only_temp)
                ->where('ext',$extension)
                ->count();
                if ($found) {
                    $c++;
                    $name_only_temp = $name_only . '_' . $c;
                }
            }
            // Update name
            $name_only =$name_only_temp;
            $name=implode('.',[$name_only,$extension]);

            $path = $media->fileSystem()->put(
                $destination . '/' . $name,
                $source,
                $visibility
            );
        } else {//dd($source);
            $path = $media->fileSystem()->putFile(
                $destination,
                $source
            );
            $media->fileSystem()->setVisibility($path, $visibility);

            $name_temp=trim(str_replace($destination,'',$path),'/\\');
            $name_temp=explode('.',$name_temp);
            $extension=array_pop($name_temp);
            $name=implode('.',$name_temp);

        }
    //

        $media->dir = $destination;
        $media->mime_type = $media->fileSystem()->mimeType($path);
        $media->size = $media->fileSystem()->size($path);
        //$media->disk = $disk;
        $media->user_id = $user_id;
        $media->fn = $name;
        $media->ext = $extension;
        $media->save();
        return $media;
    }

    /**
     * Check if a media exists both in table and in disk;
     *
     * @param Media $media
     * @return boolean
     */
    public function exists(Media $media)
    {
        return $this->existsInDisk($media) and $this->existsInTable($media);
    //
    }

    /**
     * Checks if a media exist in the database
     *
     * @param Media $media
     * @return boolean
     */
    public function existsInTable(Media $media)
    {
        return Media::where('id', $media->id)->count();
    }

    /**
     * Checks if a file for the given media exist in the disk
     *
     * @param Media $media
     * @return boolean
     */
    public function existsInDisk(Media $media)
    {
        return $media->fileSystem()->exists($media->getFullName());
    }

    /**
     * Return a media that has the given id
     *
     * @param int $id
     * @return Media
     */
    public function getMediaById($id)
    {
        return Media::where('id', $id)->first();
    }

    /**
     * Remove any disallowed characters from a directory value.
     * @param  string $path
     * @return string
     */
    private function sanitizePath($path)
    {
        return str_replace(['#', '?', '\\'], '-', $path);
    }
    /**
     * Remove any disallowed characters from a filename.
     * @param  string $file
     * @return string
     */
    private function sanitizeFileName($file)
    {
        return str_replace(['#', '?', '\\', '/'], '-', $file);
    }

    /**
     * Get the thumbnail full filename (inc folders ans extension) for any Media type [Although we have not implemented 'type' yet].
     *
     * @param Media $media
     * @return string
     */
    public function getThumbFullName(Media $media)
    {

        return trim($this->getThumbFolder($media),'\//') . '/' . $this->getThumbFileName($media).'.'.$this->getThumbFileExtension();
    }

    /**
     * Get the thumbnail file name excluding folder name but excluding extension
     *
     * @param Media $media
     * @return string
     */
    public function getThumbFileName(Media $media)
    {
        return $media->getFileName();
    }

    /**
     * Get the thumbnail file  extension
     *
     * @return string
     */
    public function getThumbFileExtension()
    {
        return $this->thumbFileExtension;
    }

    /**
     * Get the thumbnail folder 
     *
     * @param Media $media
     * @return string
     */
    public function getThumbFolder(Media $media)
    {
        return $media->getFolderName() . '/' . trim($this->thumbRelativeFolder, '/\\');
    }

   

    /**
     * Makes generic thumbnail for a media file
     *
     * @param Media $media
     * @return @see thumb
     */
    public function makeImageThumb(Media $media)
    {
        $name = $this->getThumbFileName($media);
        $ext=$this->getThumbFileExtension();
        $destination = $this->getThumbFolder($media);
        $width = $this->thumbWidth;
        $height = $this->thumbHeight;

        return $this->imageThumb($media, $width, $height, $destination, $name, $ext);
    }
    /**
     * Creates a thumbnail
     *
     * @param Media $media
     * @param int $width
     * @param int $height 
     * @param string $destination The dir
     * @param string $name The file name of the thumb (excluding dir and extension)
     * @param string $ext The extension name of the file
     * @return string Path of the created thumb
     */
    private function imageThumb(Media $media, $width, $height , $destination , $name, $ext)
    {
        

        // Save thumb at the same disk as the media to keep things simple.
        //$disk = $media->disk;
        
        $visibility = $media->fileSystem()->getVisibility($media->getFullName());

        // create new image with transparent background color
        $background = $this->getImageManager()->canvas($width, $height);
        
        // read image file and resize it
        // but keep aspect-ratio and do not size up,
        // so smaller sizes don't stretch

        
        $source=$this->getContent($media);


        $image = $this->getImageManager()->make($source);
        if ($image->width() > $this->thumbWidth or $image->height() > $this->thumbHeight) {
            //large enough
            $image->fit($width, $height, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            },'center');
        } 
        
       
        // insert resized image centered into background
        $background->insert($image, 'center');
       
        // Convert to stream
        $source = $background->stream($ext);

        // save

        $path = $media->fileSystem()->put(
            $destination . '/' . $name . '.' . $ext,
            $source,
            $visibility
        );

        return $path;

    }

    /**
     * Makes a size, overwriting the size if it exists, of a an Media image
     *
     * @param Media $media
     * @param string $size_tag A name of a registered size tag
     * @param string $type   {aspect,fit,force}If 'aspect', create an image with the specifies dimension, with respect to the aspect ratio, and using a background to fill the area if the image is smaller or the aspect ratio is different from the specified dimension. 
     *                       If 'fit' The intervention fit method will be used which could involve cropping part of the image out.
     *                       If 'force' null will be return if the image is smaller than the specified dimension; the aspect ratio is not respected.
     * @return string|null
     */
    public function makeImageSize(Media $media, $size_tag,$type='force')
    {

        $d=$this->sizeDimension($size_tag);
        if(!$d or !$d['w'] or !$d['h']){
            return null;
        }
        $width=$d['w'];
        $height=$d['h'];

        $image = $this->getImageManager()->make($this->getContent($media));
        $w=$image->width();
        $h=$image->height();

        
        
       
        
        switch($type){
            case 'aspect':

                //Is the image larger in both width and height?
                if($w > $width and $h>$height){
                    
                    $ratio_diff=abs(($width/$height)-($w/$h));
                    if($ratio_diff<$this->aspectRatioTolerance){
                        $image->resize($width, $height);//The Image should now have the correct dimension and we should really go strait to save it. .
                    }else{
                        //let us randomly assume that the width is more important
                        $image->resize($width, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    
                    $w=$image->width();
                    $h=$image->height();
                }

                // Is the image already in the required dimension?
                if ($w != $width or $h!=$height) {

                    $insert_background=false;

                    // Is the image smaller in width and height?
                    if ($w <= $width and $h<=$height) {
                        $insert_background=true;
                    }

                    // Is the image larger in width but smaller in height?
                    elseif ($w > $width and $h<=$height) {
                        // resize the image to a width of 300 and constrain aspect ratio (auto height)
                        $image->resize($width, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $insert_background=true;
                    }

                    // Is the image smaller in width but larger in height?
                    elseif ($w <= $width and $h>=$height) {
                        // resize the image to a width of 300 and constrain aspect ratio (auto height)
                        $image->resize(null, $height, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $insert_background=true;
                    }
                    else {
                        Log::warning(__METHOD__.':'.__LINE__.': msg=>Unknown image dimension issue');
                        return false;
                    }

                


                    if ($insert_background==true) {
                        // create new image with  background color ;
                        $background = $this->getImageManager()->canvas($width, $height)->fill('#cccccc');
                        // insert resized image centered into background
                        $background->insert($image, 'center');

                        $image=$background;
                        unset($background);
                    }
                }
                break;
            
            case 'fit':

                if ($w > $width or $h > $height) {
                    //large enough
                    $image->fit($width, $height, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    },'center');
                } 

                // create new image with transparent background color ;
                $background = $this->getImageManager()->canvas($width, $height);
                // insert resized image centered into background
                $background->insert($image, 'center');

                $image=$background;
                unset($background);

                break;

            case 'force':
                if ($w < $width or $h<$height){ 
                    return null;
                }
                $image->resize($width, $height, function ($constraint) {
                    //$constraint->aspectRatio();
                    //$constraint->upsize();
                });
                break;

            default:
                Log::warning(__METHOD__.':'.__LINE__.': msg=>Unknown resize type');
                // unknown type

        }


        // Prep saving
        // Save at the same disk as the media to keep things simple.
        $disk = $media->disk;
        
        $visibility = $media->fileSystem()->getVisibility($media->getFullName());
        $ext=$media->getExtension();
        $name=$media->getFileName();
        $destination=rtrim($media->getFolderName(),'/').'/'.$this->sizeFolderName($media,$size_tag);
        
        // Convert to stream
        $source = $image->stream($ext);

        // save

        $path = $media->fileSystem()->put(
            $destination . '/' . $name . '.' . $ext,
            $source,
            $visibility
        );

        return $path; //TODO: Why this method is retuning null, why is $path null.

    }


    /**
     * Reshape a given media image
     *
     * @param Media $media
     * @param int $x
     * @param  int  $y
     * @param  int $width
     * @param  int  $height
     * @param  float $rotate
     * @param  float $scale_x
     * @param  float  $scale_y
     * @return void
     */
    public function reshapeImage(Media $media,  $x=null,  $y=null, $width=null, $height=null,$rotate=null,$scale_x=null,$scale_y=null){
       
        $image = $this->getImageManager()->make($this->getContent($media));
        $changed=false;
        if($rotate){
            $image->rotate($rotate);
            $changed=true;
        }

        if(!is_null($scale_x) or !is_null($scale_y)){
            if (($scale_x!=1 and $scale_y!=1) and ($scale_x*$scale_y!=0)) {// both should not be equal 1 and any should not be zero.
                
               $this->resizeImage($image, $scale_x*$image->width(), $scale_y*$image->height());
                $changed=true;
            }
        }

        if(!is_null($width) or !is_null($height)){
            $bg_color='#ffffff';
            if($media->getExtension()=='png'){//TODO: add other formats that are transparent to this check.
                $bg_color=null;
            }
            $image=$this->cropImage($image,$width,$height,$x,$y,$bg_color);
            $changed=true;
        }


        //Save
        if($changed){
            // Convert to stream
            $source = $image->stream($media->getExtension());

            // save i.e overwrite
            $visibility = $media->fileSystem()->getVisibility($media->getFullName());

            $path = $media->fileSystem()->put(
                $media->getFullName(),
                $source,
                $visibility
            );

            //Update the size and save
            $media->size = $media->readSize();
            $media->width=$image->width();
            $media->height=$image->height();
            
            if ($media->exists) {
                $media->save();
            }
        }
        
    }

    /**
     * Returns folder name for a size for a given Media
     *
     * @param Media $media
     * @param string $size_tag
     * @return string|null This method can also return an empty string when there is not special folder for the given tag; so be careful when checking for null which is returned when the given size_tag does not exist.
     */
    public function sizeFolderName(Media $media,$size_tag){
        if($size_tag=='full'){// is the main uploaded file
            return '';
        }
         foreach($this->imageSizes() as $tag=>$px){
             if($size_tag==$tag){

                // return $px['w'].'x'.$px['h'];
                //OR
                return $tag.'_'.$px['w'].'x'.$px['h'];
             }
         }
        return null;
    }
    

    /**
     * Gets the dimension of a size.
     *
     * @param string $size_tag The tag of the image size.
     * @return array
     */
    private function sizeDimension($size_tag){
        return $this->imageSizes()[$size_tag]??null;
    }

    /**
     * Resize the given image
     *
     * @param Image $image
     * @param float $width
     * @param float $height
     * @return void
     */
    private function resizeImage(Image $image,$width,$height){
        $image->resize($width,$height);
    }


    /**
     * Crop the given Intervention Image
     *
     * @param \Intervention\Image\Image $image
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @param string $bg_color Any color format acceptable to @see \Intervention\Image\Image::fill(); e.g. '#ffffff'
     * @return \Intervention\Image\Image
     */
    private function cropImage(Image $image,$width,$height,$x=null,$y=null,$bg_color=null){

        // What is the size of the image to crop
        $image_width=$image->width();
        $image_height=$image->height();

        // Do we have a special case based on the following conditions
        if ($image_width<$width+abs($x) or $x<0   // Is the crop width outside the image
            or $image_height<$height+abs($y) or $y<0) {// Is the crop height outside the image

                
            

            // Determine the size of background image
            $canvas_width=abs($x)+$width;
            $canvas_height=abs($y)+ $height;

            // Create a background image ;
            $background = $this->getImageManager()->canvas($canvas_width, $canvas_height);
            if ($bg_color) {
                $background->fill($bg_color);
            }
            

            // Determine the insert position of the image to crop inside the background image
            $ins_x=abs(($x-abs($x))/2);
            $ins_y=abs(($y-abs($y))/2);

            // Adjust x and y with respect to the background image
            $x=abs(($x+abs($x))/2);
            $y=abs(($y+abs($y))/2);

            // Insert the image to crop into the background.
            //TODO: I can't really think now, but I think we could just return this as the crop?
            $background->insert($image, 'top-left',$ins_x,$ins_y);
            
            //TODO: Any need to unset $image here to reclaim memory? 
            //unset($image);

            $image=$background;

            //TODO: Any need for this to reclaim memory? 
            unset($background);
        }


        return $image->crop($width,$height,$x,$y);
    }

    /**
     * Resize the given Image but keep aspect-ratio and do not size up so smaller sizes don't stretch
     *
     * @param Image $image
     * @param int $width
     * @param int $height
     * @return void
     */
    private function fitImage(Image $image,$width,$height){
        if ($image->width() > $width or $image->height() > $height) {

            $image->fit($width, $height, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            },'center');

        } 
        
    }

     /**
     * Return the width and height of the image as array by using the image content. May not be adequate to run 
     * this on every page load since it may be quite heavy since it is loading the entire image first.
     * @param Media $media
     * @return array The array => ['w'=>#,'h'=>#]
     */
    public function imageDimensionFromSource(Media $media){
        if ($media->storage()->exists($media->getFullName('full'))) {
            $image = $this->getImageManager()->make($this->getContent($media));
            return ['w'=>$image->width(),'h'=> $image->height()] ;
        }
        return ['w'=>0,'h'=>0];//Incase the files has issues, it good to claim it is zero else the Media model will continue to call this heavy method whenever someone tries to get the dimension of the file.
    }

    // /**
    //  * Return the width of the image by using the image content. May not be adequate to run 
    //  * this on every page load since it may be quite heavy since it is loading the entire image first.
    //  * @param Media $media
    //  * @return int|null
    //  */
    // public function imageWidth(Media $media){
    //     return $this->imageDimension($media)['width'];
    //     // if ($media->storage()->exists($media->getFullName('full'))) {
    //     //     $image = $this->getImageManager()->make($this->getContent($media));
    //     //     return $image->width() ;
    //     // }
    //     // return null;
    // }

    //  /**
    //  * Return the height of the image by using the image content. May not be adequate to run 
    //  * this on every page load since it may be quite heavy since it is loading the entire image first. 
    //  * @param Media $media
    //  * @return int
    //  */
    // public function imageHeight(Media $media){
    //     return $this->imageDimension($media)['height'];
    //     // if ($media->storage()->exists($media->getFullName('full'))) {
    //     //     $image = $this->getImageManager()->make($this->getContent($media));
    //     //     return $image->height() ;
    //     // }
    //     // return null;
        
    // }

    /**
     * Constrain the underlying image to a given dimension. Resize the media source and replaces the source on disk. If the image is smaller than given dimension, then the image will not be touched when the $type='fit'.
     * 
     * @param Media $media
     * @param $width
     * @param $height
     * @param $type {fit,fixed} The type of the resize. 'fixed' is normal image resize. 'fit' will keep the aspect ratio.
     * @return void
     */
    public function constrain(Media $media,$width,$height,$type='fixed'){
        // but keep aspect-ratio and do not size up,
        // so smaller sizes don't stretch

        
        $source=$this->getContent($media);


        $image = $this->getImageManager()->make($source);
        $changed=false;
        switch($type){
            case 'fit':// Respects aspect ratio and do not stretch.
                if ($image->width() > $width or $image->height() > $height) {
                    //large enough
                    $image->fit($width, $height, function ($c) {
                        $c->aspectRatio();
                        $c->upsize();
                    },'center');

                    $changed=true;
                } 
                break;
            case 'fixed': //No respect for aspect ratio
                $image->resize($width,$height);
                $changed=true;
                break;
            
        }

        if($changed){
            // Convert to stream
            $source = $image->stream($media->getExtension());

            // save i.e overwrite
            $visibility = $media->fileSystem()->getVisibility($media->getFullName());

            $path = $media->fileSystem()->put(
                $media->getFullName(),
                $source,
                $visibility
            );

            //Update the size and save
            $media->size = $media->readSize();
            $media->width=$image->width();
            $media->height=$image->height();
            if ($media->exists) {
                $media->save();
            }
        }
        

    }



    /**
     * Get raw string file content
     *
     * @param Media $media
     * @return string
     */
    private function getContent(Media $media){
        return $media->fileSystem()->get($media->getFullName());
    }



}