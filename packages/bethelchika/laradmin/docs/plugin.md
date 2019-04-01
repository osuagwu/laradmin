# Plugin manager
The plugin manager allows you to extend the capability of your Laravel app using other packages that you can easily turn on and off.

The plugin manager instance can be used to perform several operations. The instance can be resolved from Laravel IOC:
```php
$laradmin=app('laradmin');
$pluginmanager=$laradmin->pluginManager;
```


## Configuration.
The default plugin folder is /plugins relative to application root. The folder can be changed by changing Laradmin config 'plugins_path' or setting the 'LARADMIN_PLUGINS_PATH' in env file. Set these to empty string to use the default path.

## Installing  and unintalling a plugin
Copy the plugin folder to the plugin folder. Login to the Laradmin admin area, from the sidebar select plugins under General and follow the process.


## Updating a plugin
Updating a plugin is not implemented yet. So to update a plugin, first uninstall the previous installation and then install the new one. NOTE THIS WILL CURRENTLY MAY CAUSE LOSS OF DATA AS THE UNINSTALLATION MIGHT MIGHT DELETE THEM.

## Writing  a pluging
### Concept and Plugable
Writing a plugin is not differenct from writing a package. The main difference is that the plugin must have a class that implements the ```php \Laradmin\Plugin\Contracts\Plugable interface ```. This interface is like a service provider for a plugin but id does not replace a service provider. In fact a servicce provider may still be important. Service provider for the plugin must be register in the register method of the plugable. Review the description methods if the interface for further instruction.

The functions of the plugable are called during variouse activities of the  plugin with parameters  
```php 
$pluginmanager
```
e.g.
```php
public function install($pluginmanager,$tag)....
```
Where tag is the name of the plugin.

The register method is however called thus:
```php
public function register(Application,$tag)....
    //register plugin service provider
```
The register method of the  

## Composer.json
All plugins must have a 'composer.json' file in the root folder of the plugin. The name entry on the composer.json is used as the plugin folder relative the plugins folder. The composer.json should contain psr-4 entries for all classes that plugin is installing. The file must also have extras entry that includes the 'plugable' with value equal to the fully qualified class of the plugin plogable the implements the plugable interface.

Here is an example json file:
```json
{
    "name": "bethelchika/comicpic",
    "description": "Fantastically ...",
    "type": "project",
    "license": "MIT",
    "authors": [
        {
            "name": "Bethel Chika Osuagwu",
            "email": "bethel.osuagwu@gmail.com"
        }
    ],
    "minimum-stability": "dev",
    "require": {
        "doctrine/dbal": ">=2.5",
        "intervention/image":">=2.4"
    },
    "autoload": {
        "psr-4": {
            "\\BethelChika\\Comicpic\\": "src/"
        }
    },
    "extra":{
        "plugable":"\\BethelChika\\Comicpic\\ComicpicPlugable",
        "title":"Comicpic"
    }
}

```


### Admin
Add menu items for you plugin admin to the menu. The create corresponding routes and view as normal as in Laravel packages. Also the controllers are as normal except that instead of calling return view(..), you should call 
```php 
return $pluginmanager->adminView(....) ;
```

## User settings page
In addition to creating admin pages for a plugin, you can create also a user settings page.The following are required to create a user settinsg page:
- create a menu item with parent tag of 'user_settings', e.g in the boot method of your service provider. see menu documentation for how to create a menu. TODO: Note that because the rest of the menu items are loaded later, your menu will be first on the list; This should be fixed in the future.
- Add a corresponding Laravel route to the menu.
- Create a controller  and method for the route. 
- In the method of your controller, return your 'view' thus:
```php 
return $pluginmanager->userView(....) ;
```
- Done.

A few things to note: When the plugin has php error, the whole page will break because they is not processed separatly. The view of the plugin is rendered as part of the user settings page which already is wrapped with the '.container(-fluid)' css class of Bootstrap, so no need to add another one; you can just use 'row's and 'col's css classes as normal. The the view content of the plugin is also wrapped with the '.mainbar' css class which add content padding tot he content as well as display it on the right side of sidebar. So if you want to use the standard '.section' css class inside your view and want the section to offset the content padding of '.mainbar' then you can add the following css classes to the '.section' 
- 'section-offset-mainbar-top' : to offset the top margin
- 'section-offset-mainbar-sides' : to offset the left and right margins
There are offcourse not neccessary.

