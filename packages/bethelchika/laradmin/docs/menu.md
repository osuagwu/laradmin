# Menu
## Concepts
Menu (class=Menu):  A navigation item whos children are menu items.
Munu item (class=MenuItem): Displayable nagigation item who is a child of menu; it can have a link.
Nanigation (class=Navigation): A list of menus.
## Initialisation
All the properties of the `Navigation` class are all static and one of them stores the menus and menu items. To initialise the navigation simply make a call the `Navigation` class thus:
```php
Navigation::init('navigation.nav');
```
Or
```php
$nav=new Navigation('navigation.nav');
$nav->init();
```
where 'navigation.nav' (default) is the navigation file where you want navigations to be stores. 

## Creating a menu item
### Using navigation object
The easist way to create a menu is to use the `Navigation` class' `create` method.
```php
Navigation::create('Contact','contact','primary'); //
```
The above will create a menu item named 'Contact' with a tag 'contact' in the menu with a tag, 'primary'. If the menu with tag 'primary' does not exist, it will be created.

```php
$item =Navigation::create('Test 2','test2','primary.test1',['url'=>'http://www.bbc.co.uk']);
$item->cssClass='btn lg';
```
The above will create a menu item with a lable 'Test 2' with tag 'test2' as a child item of 'test1' which is in the 'primary' menu. Notice that the Navigation actually returns the menu item allowing it to be further customised.

You can also pass a forth parameter to set some public properties of the item 
```php
Navigation::create('Test 4','test4','sidebar.test1.test2.test3',[
    'url'=>'http://www.bbc.co.uk',
    'cssClass'=>'testitem ...',
    ...
    ]);
```
Note that if any oof the 'test1' - 'test3' does not exist, it will be created; that is the advantage of using the `Navigation` class.

### Manually
Let us manually create two menu items and add both to primary menu.
```php
// Create a menu
$menu = new \BethelChika\Laradmin\Menu\Menu('primary', 'primary');

// Add menu item
$item_about=new \BethelChika\Laradmin\Menu\MenuItem('About', 'about');
$item_about->namedRoute='about';
$menu->addChild($item_about);

// Add menu item
$item_contact=new \BethelChika\Laradmin\Menu\MenuItem('Contact', 'contact');
$item_contact->namedRoute='contact-us-create';
$menu->addChild($item_contact);

// Add menu to navigation
Navigation::addMenu($menu);
```

## Removing menus and menu items
### Remove all
```php
Navigation::clearAll();
```

### Remove specific items
The `Menu` and `MenuItem methods for removing selected items exists but are not tested.

## Storing
You can store the current menu to disk so that it can be loaded again on start up thus:
```php
Navigation::store();
```

## Rendering for Blade
Names of pre-defined menus include 
'primary'| Main menu
'user_apps' | applications that users can interact with
'user_settings'| User settings
'app_settings'| Application settings menus
'sidebar' | Side bar menus
'footer'| Footer menus
'plugin_admin'| admin settings for a plugin
'admin, admin.apps, admin.general' etc | Admin menus
 'user_apps' | User apps menus for the front end.
 'page_family' | parent and and children of a current page. 

To render the menu with a tag primary.
```php
 @include('menu', ['tag' => 'primary'])//
 ```
 Or if under Laradmin
 ```php
  @include('laradmin::menu', ['tag' => 'primary']);//Note that 'tag' dot separated tags: e.g primary.comicpic.settings
  ```

  The output is a list of 'li' which may according have children etc. SO you will need to wrap the list in a 'ul' and build a menu around it. out put 'li' elements allows for flexibility in the menu design and addition of items in the menu externally.

 ## Properties
 Please see `NavigationItem` `MenuItem` and `Menu` classes for the definition of all the properties

### url and namedRoute properties for menu items
Menu item link can c set using the url of the namedRoute property. If set the url property overides the namedRoute property. The namedRoute is passed to laraven functions to convert to a full link

### iconImage and iconClass properties for menu items
These classes are used to set icon to the item. if set the iconImage overrides the iconClass. The iconClass can be a font awesome icon class of similar while the iconImage is an image url.

### hidden property for both menus and menu items
The 'hidden' property has 4 possible values, thus:
0:Not hidden
1:Hidden from signed in users only
2:hidden from guest users only
3:hidden from everyone