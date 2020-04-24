# Media Manager
The media system allow a file to be associated to models. One media can be associated with multiple models. A media file will be deleted when it is not associated with any more models.

## Associating media to models
To associate a media to a model, you should call the medias() method on the model. to access this method, the model must use the \BethelChika\Laradmin\Media\Traits\Mediable trait.

```php
use BethelChika\Laradmin\Media\Traits\Mediable;
class Property extends Model
{
    use Mediable;
    ...
}

$property->medias()->save($media,['tag'=>'image']);
```

Typically you will use files that are downloaded to you system.

```php
$mediamanger=app('laradmin')->mediaManager;
$media=$mediamanager->fromSource($request->input('file'),$path,null,'public','public',$user);
```
The first argument is the uploaded file, the second, $path is the path where the file should be stored, the third is the name you want to manually assign to the file (set to null for auto generation of name). The forth is the disk and the last is the visibility.s

```php
...
$property->medias()->save($media,['tag'=>'image']);
```
In addition to the *tag*, you can also provide, *title*, *description*, *index_tags* and *order*.

The tag *image* could be used to retrieve medias later, thus.

```php
...
$property=Property::find(1);
$medias=$property->medias()->where('tag'=>'image')->get();
```

This will return all the *Property* model medias with tag 'image'.

For images, you can create different images sizes from the media file. Image sizes are identified by their given names. Do the following to create an image size called slide:
```php
$media->makeImageSizes('slide','aspect');
``` 
The second parameter 'aspect' ensures that the aspect ratio of the image is respected and no part of the image will  be lost. The other options includes *fit* which will use the fit() method of Intervention image and *force* which will force the creation of the required size without taking the aspect ration into consideration.



## URI
You can get the url of a media in a public disk thus:
```php
$url=$media->url('slide');
```

The argument is the image size of interest. You can also get the absolute filename.

```php
$path=$media->getAbsoluteFullName('slide');
```

To check that a file exists in the disk for the requested size, you can use exist(), or hasAny() which can take an array of sizes and returns true if any of them exists.
```php
if($media->hasAny(['slide','full'])){
    // Image exists in disk
}
```

## Custom sizes
The original media file size is regarded as *full* size. So this name is reserved. The media system when instructed can create a special thumb image which are called *thumbs*(likely going to be removed). This name is also reserved. You can register other size names as you desire. You can also override non-reserved tags such as *_cover_photo_sm_* which is used for small size of a cover photo. To override it, just register the tag. Lets register an image size and call it 'medium', with size 950 x 300 px 

```php
    //Register image sizes
    $mediamanager->registerImageSize('medium',950,300);
```
Now you can create the size and obtain the url:
```php
$media->makeImageSizes('medium','fit');
$url=$media->url('medium');
``` 

 You can only permanently register image sizes when your application is running in console context. A good place to register image sizes is in the boot method of a service provider. Lets register two image sizes called 'slide' and 'thumb' with sizes 660 x 440 px and 150 x 100 px. 
```php
    // Register image sizes
    if($app->runningInConsole()){
        $mediamanager->registerImageSize('slide',660,440,true);
        $mediamanager->registerImageSize('thumb',150,100,true);
    }
```
The last argument specifies that the image size should be store permanently.

## Deletion
To delete/detach a Media from a model, just call delete() on the corresponding media instance. 
```php
$property=Property::find(1);
$media=$property->medias()->first();
$media->delete();
```
This will detach the Media and deletes the associated files if there is not other model attached to the Media.

``` 
Note: If the model with associated media has the softDelete functionality, you should set the *deleted_at* property yourself when soft deleting to avoid the media detachment/files being deleted. Unless of course it is desired that the media is detached and files are deleted on soft delete
```

```php
...
$property=Property::find(1);
$property->deleted_at=Carbon::now();
$property->save();

...
```
This ensures that when a soft deleted model is restored the associated medias are still attached. 