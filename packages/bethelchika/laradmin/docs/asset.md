# Asset Manager
## Reference to assest manager
```php
$assetmanager=app('laradmin')->assetManager;
```
## Register Asset
You can register assests crudly by registering a string using registerAsset of AssetManager class.
```php

$assetmanager->addAsset('head-styles','unique_tag_of_this_asset','<style>.dropzone{background-color:yellow;}</style>');
//OR
$assetmanager->addAsset('head-styles','unique_tag_of_this_asset','local_or_external_link_tag_to_asset');

$assetmanager->addAsset('footer-scripts','unique_tag_of_this_asset','local_or_external_script_tag_to_asset');
        
```
The First augument is the Blade stack you want the script to be placed in; the second is the unique id o the asset. And the third is the asset script, css tags, or url to them.
The predefined Blade stacks are:
```php
 public static $stacks=[
        'head-styles',
        'footer-scripts-after-library',
        'footer-scripts',
        'meta',
    ];
```



Registration can be done in a service provider if you want the assest to be available in all pages or you can make checks in widthin the provider to serve the asset on certain pages only. But you can be more specific and register the asset in controller contructors or methods.
## Render assests
To render all assets registered make sure that @include('laradmin::inc/asset_manager/asset.blade.php') is in your template e.g in master template so that it will be available to all child templates. This file only renders predefined stacks so if you registered to a custom stack, you will need to render it yourself by calling
```php
$assetmanager->getAssetString($stack,$tag);
```
and specify the stack and the unique tag it was regestered to.
## Register body class
```php
$assetmanager->registerBodyClass($class);
```

## Render body class
To get the classes registered.
```php
$classes=app('laradmin')->assetManager->getBodyClassesString();
```
In blade
{{app('laradmin')->assetManager->getBodyClassesString()}}


## Logo type
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