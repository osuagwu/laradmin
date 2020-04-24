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
The can() and isDisallowed() methods, as their names suggest, are not opposits. The can() method checks that access is explicitly given accept if the given user is 'super' or an unrestricted admin and should be preferred when access to a source is denied by default. But the isDisallowed() method checks that access is not explicitly denied. It should be used when access is allowed to a source by default. 

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

The 'file', 'url', and 'model' can be linked and stored in the *sources* table to form \BethelChika\Laradmin\Source models.

The 'table', 'route', 'route_prefix' and 'page' are read from Laravel and not linked on the *sources* table.

### Checking source permission
To check for access to source you will need the 'type' and 'id' of the source as was shown earlier in route access check example. The  'type' and 'id' are straightforward for source linked and stored in the *sources* table. The 'type' for these sources is the class, \BethelChika\Laradmin\Source, and the 'id' is the corresponding model key (ie. id)

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
If you have the model tha uses the table 
```php
// 
$source_id=Source::getTableSourceIdFromModel(new \BethelChika\Laradmin\UserMessage());
$access=$perm->can($user,'table',$source_id,'update');
```

 If you do not have the model that uses the table
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
See control panel, and use *Source/types* menu for how to manage permissions.

## Policies and gates
Laradmin provides some Laravel policies and gates which use the permission system to provide authorisation  for source types of *model* and *table*. These are already implemented in the admin and profile pages. You can easily check access in controllers and views etc:
```php
class UserProfileController extends Controller
{
    
    function index(){
        $certain_user=\BethelChika\Laradmin\User::find(3);
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

Tow important abilities are available to allow you to check if a user has admin powers and if a user is restricted.
- *administer* - Check if a user has admin powers
- *user.check* - Check that a user is not disabled or banned.

```php
Gate::check('administer');
$this->authorize('user.check');// In a controller
//etc
```

For control panel access you can use a gate which exploits the *route_prefix* source type:
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

The ability *cp* specifically checks if a user has access to the */cp* route prefix. By default it is only administrators and the super user has access to this route prefix. Therefore one can also use the *administer* ability to check if a person has access to the control panel. 
```

Check access to edit a page in a view:
```php
@can('update',$page)
    //Can edit
@endcan
```
Where page is an instance of *\BethelChika\Laradmin\WP\Models\Page*

### General Model policy
The ModelPolicy is a general policy  model when useful when there is no need to create a complicate special policy for a model. 

Note that this policy class should be used when a user should only have access 
to a model if the user have explicit permission for source type=model for this model or
owns the model or have admin powers. If none of these applies access will be denied. So 
this policy is not suitable for models that everyone is allowed to access without 
explicit permission. User access can be controlled for a particular model by creating 
permission using the source type=model in the control panel.
    

The policy can be accessed through the abilities: 
1. *model.create* 
2. *model.view*
3. *model.views* Checks if a user can list the model
4. *model.update*
5. *model.delete*

Example usages:In a controller
```php
$this->authorize('model.create',UserMessage::class); //The second input is the class name

// If you have model instance
$user_message=new \BethelChika\Laradmin\UserMessage;
$this->authorize('table.update',$user_message);//

//Or Using gate facade
Gate::check('model.create','user_messages');

// 
Gate::authorize('model.update',$user_message);
```



### General table policy
This policy class is similar to ModelPolicy (See ModelPolicy::class for more details) but 
for tables. It denies by default unless explicit access is given or user has admin 
powers. Unlike ModelPolicy it is of course not possible to have instance of a table 
owned by a user; so access cannot be granted by ownership in TablePolicy. 
```
This is important because if you do not want the ModelPolicy behavior of allowing access to users if they own a model, then use the TablePolicy to control access to the resource concerned. 
```
The available abilities are:
1. *table.create* 
2. *table.view*
3. *table.views* Checks if a user can list the table.
4. *table.update*
5. *table.delete*

Example usages:In a controller
```php
$this->authorize('table.create','user_messages'); //The second input is the table name

//Or Specify the connection
$this->authorize('table.create',['user_messages','my_connection']);//

//Or Using gate facade
Gate::check('table.create','user_messages');

// Or Specify the connection
Gate::authorize('table.delete',['user_messages','my_connection']);
```

### User gate
You may want to grant access but would like to first check that a user is not disabled or banned etc. You should use the ability 'user.check' for this.
```php
// In a controller 
$this->authorize('user.check');
//Or
Gate::authorize('user.check');
// OR
Gate::allows('user.check');
//etc
```

## Tips
### Custom group permission
If you have a group say *Editors* and you wnt to check that a user belongs to this group before allowing them to perform an action. There are a few ways to achieve this. 

- You could simple add the permission for a *Editors* to the models, tables, route prefix or any source type they will be editing. For example, if it is a model, after adding the permission, you could do the following in your controller.
```php
$this->authorize('model.update',$model);//Where $model is an instance of the model.
```
    In the above code, the current user can only be authorized if she owns the model, has admin powers or is in editors group. If you do not want to give authorization based on ownership then you should not implement the permission using model source type, you could use table or route_prefix, route etc.

- You can also create an policy or simply an ability such as *editor.check* which will both check that the user is in the group *Editors* and she is not banned or disabled, thus:

```php
Gate::define('editor.check',function(User $user){
    if(!$user->can('user.check') or !$user->isMemberOf('editors')){
        return false;
    }
    
});
```
With this method you do not need to enter any permission to the *Editors* group.

## User Groups
U can use the control panel to add a user to a group. But you can also do this programmatically thus:
```php
$user=User::find(5);
$user->addToGroup('editors'); // To add the user
$user->removeFromGroup('editors');// to remove the user
```
## TODO:
1. Add permission for a single model: Note that it seems that all we need to do ti make this work is to just add a view that let us dd the permission where source_type=MODEL_CLASS and source_id=MODEL_ID.
     




