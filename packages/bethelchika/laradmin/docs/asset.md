# Asset
## Asset Manager
### Reference to assest manager
```php
$assetmanager=app('laradmin')->assetManager;
```
### Register Asset
You can register assests crudly by registering a string using registerAsset of AssetManager class.
```php

$assetmanager->addAsset('head-styles','unique_tag_of_this_asset','<style>.dropzone{background-color:yellow;}</style>');
//OR
$assetmanager->addAsset('head-styles','unique_tag_of_this_asset','local_or_external_html_link_tag_to_asset');

$assetmanager->addAsset('footer-scripts','unique_tag_of_this_asset','local_or_external_script_tag_to_asset');
        
```
The First argument is the Blade stack you want the script to be placed in; the second is the unique id of the asset. And the third is the asset script, css tags, or url to them.
The predefined Blade stacks are:
```php
 public static $stacks=[
        'head-styles',
        'head-styles-library',
        'footer-scripts-library'
        'footer-scripts-after-library',
        'footer-scripts',
        'meta',
    ];
```



Registration can be done in a service provider if you want the assest to be available in all pages or you can make checks in widthin the provider to serve the asset on certain pages only. But you can be more specific and register the asset in controller contructors or methods.
### Render assests
To render all assets registered make sure that @include('laradmin::inc/asset_manager/asset.blade.php') is in your template e.g in master template so that it will be available to all child templates. This file only renders predefined stacks so if you registered to a custom stack, you will need to render it yourself by calling
```php
$assetmanager->getAssetString($stack,$tag);
```
and specify the stack and the unique tag it was regestered to.
### Register body class
```php
$assetmanager->registerBodyClass($class);
```

### Render body class
To get the classes registered.
```php
$classes=app('laradmin')->assetManager->getBodyClassesString();
```
In blade
```
{{app('laradmin')->assetManager->getBodyClassesString()}}
```


### Logo type
The logo type methods of the asset manager allows for addition of extra name to the original logo name thus: "original{{type}}.svg".
Set the logo type by calling the following method.
```php
$assetmanager->registerLogoType('hero');
```
Read the logo type by calling the following method.
```php
$logotype=$assetmanager->getLogoType('-');//...logo-hero.svg
```
The method accepts a string which is added to the beginning of the returned name

Call the following function to check if logotype has been set:
```php
if($assetmanager->hasLogoType()){
    ...
}
```

## Style

### User area styles
To make changes to the style for the user area, e.g. change the color scheme, you will need to compile the raw asset yourself and replace the original.

To do this, copy the folder */packages/bethelchika/laradmin/resources/user/*  or */vendor/bethelchika/laradmin/resources/user/* ,depending on your installation,to a location of your choice. Or you could use the following Artisan command which will copy it to your resources folder, */resources/laradmin/user/*
```
php artisan vendor:publish --tag=laradmin-raw-asset
```
You should then compile the asset files as you wish and move the output to your */public/vendor/laradmin/user/*. Note that the main output should be named *user.css* other you will have to publish the views and change the html link tag for the css.

Assuming your have used the Artisan command above, you could put the following line in your Laravel mix to help compile the SCSS and place the CSS in the correct location.
```js
mix.sass('resources/laradmin/user/sass/app.scss', 'public/vendor/laradmin/user/css/user.css');
```
You will need to make sure that Boostrap is available and its path is correct in the *resources/laradmin/user/sass/app.scss*

You should use the correct Bootstrap version when performing the compilation. The correct version may be stated in */{packages}/bethelchika/laradmin/resources/user/sass/app.scss*. Otherwise you should see the version of the corresponding Bootstrap Javascript loaded via CDN in the user area pages.

### Section styles
If you are using Wordpress and are using your own styles rather that *user.css* that came with Laradmin then you might be interested in using the *section.scss* found in copy the folder */packages/bethelchika/laradmin/resources/user/*  or */vendor/bethelchika/laradmin/resources/user/* ,depending on your installation. You can compile and use this on your WP pages including hero and homepage look fine out of the box.