# Permission
Laradmin permission system is used to implement authorisation.
The permission object can be accessed thus:
```php
$perm=app('laradmin')->permission;
```
To check if a user has permission, the `can()` and the `isDisallowed()` methods can be used.
```php
use BethelChika\Laradmin\Source;

//Check if a user has read access to the current route
$route=app('router')->current();
$source_id=Source::getRouteSourceId($route);// Get the signature of the route
$source_type='route';
$access =$perm->can($this->user,$source_type,$source_id,'read');
```
The can() and isDisallowed() methods, as their names suggest, are not opposits. The can() method checks that access is explicitly given accept if the given user is 'super' or an unrestricted admin and should be prefered when access to a source is denied by default. But the isDisallowed() method checks that access is not explicitly denied. It should be used when access is allowed to a source by default. 

A similler check as above can be made for a url if it has been linked to the source table.
```php
use BethelChika\Laradmin\Source;

//Check if a user has read access to the current url
$url=request()->url();
$source=Source::where('type','url')->where('name',$url)->first();
if($source){
    $access =$perm->isDisallowed($this->user,$source->type,$source->id,'read');
}
```

You can also check for an access to route prefix, which perform the authorisation in all routes with a given prefix.


## Middleware
The permission system provides a middleware 'pre-authorise', which automatically checks for access to routes, route prefix and urls. Apply the this middleware wherever you want this automatic check. Note that for a url if access is denied at any of the path levels, the middleware will denied access. For example for a url, http:://webferendum.com/check/access, the middlewre will check access in for the following sub urls including the protocol:
http
http:://webferendum.com
http:://webferendum.com/check
http:://webferendum.com/check/access

and will deny access if access is denied in any of the checks.

## Source
A source is essentially a resource in laradmin on which we can add permission. Laradmin provides default source types including:
'table'=>'Table',
'file'=>'File',
'route'=>'Route',
'route_prefix'=>'Route prefix',
'url'=>'URL',
'page'=>'Page',
'model'=>'Model'.

The 'file', 'url', and 'model' can be linked and stored in the sources table to form \BethelChika\Laradmin\Source models.

The 'table', 'route', 'route_prefix' and 'page' are read from Laravel and not linked to the sources table.

### Checking source permission
To check for access to source you will need the 'type' and 'id' of the source as was shown earlier in route access check example. The  'type' and 'id' are straightforward for source linked and stored in the sources table. The 'type' for these sources is the class, \BethelChika\Laradmin\Source, and the 'id' is the corresponding model key (ie. id)

```php
use BethelChika\Laradmin\Source;

//Check if a user has read access to a file
$source=Source::where('type','file')->where('name','/storage/v.mp4')->first();
if($source){
    $access =$perm->can($this->user,$source->type,$source->id,'read');
}
```

For sources which do not have \BethelChika\Laradmin\Source models, the 'type', and 'id' are obtained differently. 
For route source type see the first example.

For a route prefix source type:
```php
$route=app('router')->current();
$prefix=$route->getPrefix();
$source_type='route_prefix';
$source_id=$prefix;
$access=$perm->isDisallowed($this->user,$source_type,$source_id,'read');

```

For a page source type:
```php
use BethelChika\Laradmin\WP\Models\Page;

$page = Page::published()->where('post_name', 'slug')->first();
$access=$perm->isDisallowed($user,get_class($page),$page->getKey(),'read')

```

For a table source type: 
If you have the table model
```php
// 
$source_id=Source::getTableSourceIdFromModel(new \BethelChika\Laradmin\UserMessage());
$access=$perm->can($user,'table',$source_id,'update');
```

 If you do not have the table model
 ```php
use BethelChika\Laradmin\Source;

$database_connection='mysql';
$database='laradmin';
$table_prefix='';
$table_name='user_messages';
$source_id = Source::getTableSourceId($database_connection,$database,$table_prefix,$table_name);
$access=$perm->can($user,'table',$source_id,'update');
```

## Adding permission
See control panel for source for how to manage permissions.

## Policies and gate
Laradmin provides some Laravel policies and gates which use the permission system to provide authorisation  for source types of *model* and *table*. These are already implemented in the admin and profile pages. You can easily check access in controllers and views etc:
```php
class UserProfileController extends Controller
{
    
    function index(){
        //Check if the current user has read access to the certain user
        $this->authorize('view', $certain_user);
        ...
    } 
}
```
The above will automatically check access using for the *table* and *model* for the `$certain_user` object. You can check for *update*, *create* and *delete* access aswell.

More examples, 
for UserMessage controller:
```php
class UserMessageController extends Controller
{
    
    function destroy(UserMessage $message){
        //Check if the current user has delete access to the message
        $this->authorize('delete', $message);
        ...
    } 
}
```

for control panel you can use a gate which exploits the *route_prefix* source type:
```php
class ControlpanelController extends Controller
{
    function index(){
        //Check access to control panel
        if (Gate::denies('cp')) {
            abort(403);
        }
    }
}
```

Check access to edit a page in a view:
```php
@can('update',$page)
    //Can edit
@endcan
```




