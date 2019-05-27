<?php
namespace BethelChika\Laradmin\Media;

use BethelChika\Laradmin\Media\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemManager as FileSystem;

class MediaManager
{


    /**
     * The default width of thumbnails in pixels
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
     * The folder name relative to a Media's folder where thubnails are kept
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
     * Construct a new media
     *
     * @param Factory $filesystem
     */
    public function __construct(FileSystem $filesystem)
    {
        $this->filesystem = $filesystem;
     //$this->model=$model;
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
     * @param string $name The name to used to save the file. This excludes the directory name
     * @param string $disk
     * @param string $visibility
     * @return Media 
     */
    public function fromSource($source, $destination, $name = null, $disk = null, $visibility = null, Model $user = null)
    {
    //$im=$this->getImageManager();
    //print_r($this->filesystem);
      //$name='change';
        $disk = $disk ? $disk : 'local';
        $visibility = $visibility ? $visibility : 'private';
        $user_id = $user ? $user->id : 1; //Need to change the default to CP_ID

        $media = new Media;

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

            $path = $this->filesystem->disk($disk)->put(
                $destination . '/' . $name,
                $source,
                $visibility
            );
        } else {//dd($source);
            $path = $this->filesystem->disk($disk)->putFile(
                $destination,
                $source
            );
            $this->filesystem->disk($disk)->setVisibility($path, $visibility);

            $name_temp=trim(str_replace($destination,'',$path),'/\\');
            $name_temp=explode('.',$name_temp);
            $extension=array_pop($name_temp);
            $name=implode('.',$name_temp);

        }
    //

        $media->dir = $destination;
        $media->mime_type = $this->filesystem->disk($disk)->mimeType($path);
        $media->size = $this->filesystem->disk($disk)->size($path);
        $media->disk = $disk;
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
        return $this->filesystem->disk($media->disk)->exists($media->getFullName());
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
    private function imageThumb(Media $media, $width = null, $height = null, $destination = null, $name = null, $ext)
    {
        

        // Save thumb at the same disk as the media to keep things simple.
        $disk = $media->disk;
        //dd($this->filesystem->disk($disk)->get($media->fn));
        $visibility = $this->filesystem->disk($disk)->getVisibility($media->getFullName());

        // create new image with transparent background color
        $background = $this->getImageManager()->canvas($width, $height);
        
        // read image file and resize it
        // but keep aspect-ratio and do not size up,
        // so smaller sizes don't stretch

        //$source = $this->filesystem->disk($disk)->get($media->getFullName());
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

        $path = $this->filesystem->disk($disk)->put(
            $destination . '/' . $name . '.' . $ext,
            $source,
            $visibility
        );

        return $path;

    }

    /**
     * Return the width of the image
     * @param Media $media
     * @return int
     */
    public function getWidth(Media $media){
        $image = $this->getImageManager()->make($this->getContent($media));
        return $image->width() ;
    }

     /**
     * Return the height of the image
     * @param Media $media
     * @return int
     */
    public function getHeight(Media $media){
        $image = $this->getImageManager()->make($this->getContent($media));
        return $image->height() ;
        
    }

    /**
     * Get raw string file content
     *
     * @param Media $media
     * @return string
     */
    private function getContent(Media $media){
        return $this->filesystem->disk($media->disk)->get($media->getFullName());
    }

}