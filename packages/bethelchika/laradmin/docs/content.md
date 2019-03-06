# Content Manager
The content manager is very under construction with many implementation still to do.
## Reference to content manager
```php
$contentmanager=app('laradmin')->contentManager;
```
## Sub app Name
You can set the sub app name which appears in fron pf the logo to indicate which app is currently running.
Set the sub app name by calling the following method.
```php
$contentmanager->registerSubAppName('Comicpic',$url);
```
Here the $url is the link to the sub app. Read the sub app name by calling the following method.
```php
$subappname=$contentmanager->getSubAppName();
$subappname=$contentmanager->getSubAppUrl('/'); 
```
The $contentmanager->getSubAppUrl() accepts a default url to return if none is defined
Call the following function to check if sub app name has been set:
```php
if($contentmanager->hasSubAppName()){
    ...
}
```

