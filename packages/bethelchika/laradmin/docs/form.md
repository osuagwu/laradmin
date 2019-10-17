# Form
The Laradmin form is designed with plugins and external packages in mind. They allow a module to create a form where fields on the form can come from other modules, like core, plugins and packages. So the form is not designed to simplify form process exactly but to create a collaborative form. If your intention is not to create a collaborative form that other people can contribute to, you can still use form if you want to take advantage of some of its features; e.g if you cannot be bordered to write the HTML of the form yourself including displaying of errors.
## Creating custom form 
Create form simply thus:
```php

use \BethelChika\Laradmin\Form\Form;
use \BethelChika\Laradmin\Form\Field;
use \BethelChika\Laradmin\Form\Fieldset;
use \BethelChika\Laradmin\Form\Group;
class MyFormController{
    $form=new Form($pack,$tag,$mode);
}
```

You can add fields to the form by calling the addField(...) method. You can add Field and Fieldset. You can also add  Group as a field.  A Field is the basic control of the form. The fields can instead be added to a Fieldset with the Fieldset then added to the form. A Group is different although it is added as a field; it is not a control rather a grouping of fields and fieldsets. Each Field/Fieldset should (not compulsory) have group name(the group property) which define which group it belongs to. the group belonging is deterimined by comparing the 'group' property of Field/Fieldset  with the 'name' property of defined Groups. Fields are displayed according to their group belongings and all those without groups are display together. You can initialise a form in 'index' or 'edit' mode and this is specified using the $mode parameter. The 'index' mode is for viewing the form while the 'edit' mode is for editing it. The 'order' property of Field, Fieldset and Group, is a floating point number used to sort them on the form.
 Add fields to the form:
 ```php
$field=Field::make([   'type'=>'text',
    'name'=>'comicpic_auto_feeds',
    'label'=>'Automatically post a feed',
    'group'=>'comipic_settings',
    'order'=>0,
    'help'=>'Do you want this item to be posted to feed when published?',
    'placeholder'=>'Please choose',
    'class'=>'',
    'unit'=>'',
    'value'=>'Test answer',
    'options'=>[],
    'rules' => 'required|min:3',
    'messages'=>['required'=>'Please specify if you want automatic feeds when you publish',
                'min'=>'Please enter at least 3 characters'
            ]
]);
$form->addField($field);
 ```
 Or
 ```php
 $field=new Field('comicpic_auto_feeds');
    $field->type='text';
    $field->label='Automatically post a feed';
    $field->group='comipic_settings',
    $field->order=5,
    $field->help='Do you want this item to be posted to feed when published?',
    $field->placeholder='Please choose',
    $field->class='',
    $field->unit='',
    $field->value='Test answer',
    $field->options=[],
    $field->rules='required|min:3',
    $field->messages=['required'=>'Please specify if you want automatic feeds when you publish',
                'min'=>'Please enter at least 3 characters'
            ]
]);
$form->addField($field);
 ```
Group and Fieldset can be similarly be added. Use the addField(...) of Fieldset to add Field/s to it, 

To label a group of fields, you must add a Group to the form and set a none empty label property for it. Below are some examples of form items.
```php

       // Example of a radio field
        $test1=Field::make([   'type'=>Field::RADIO,
        'name'=>'comicpic_screen_name_2',
        'label'=>'Choice',
        'order'=>5,
        'help'=>'Help text',
        'class'=>'',
        'unit'=>'',
        'value'=>'b',
        'options'=>['a'=>'A','b'=>'B','c'=>'C'],
        'rules' => 'required',

        ]);

        // Example of a checkbox
        $test2=Field::make([   'type'=>Field::CHECKBOX,
        'name'=>'comicpic_screen_name_check',
        'label'=>'Check',
        'group'=>'comicpic_author',
        'order'=>5,
        'help'=>'Help text',
        'class'=>'',
        'unit'=>'',
        'value'=>['a','d'],
        'options'=>['a'=>'A','b'=>'B','c'=>'C','d'=>'D'],
        'rules' => 'required',

        ]);
```
        Note that for checkbox 'options' property, you should always use key=>value pairs even for only one checkbox item e.g: 
```php
         $test3=Field::make([   'type'=>Field::CHECKBOX,
        'name'=>'terms_conditions',
        'label'=>'Accept terms and conditions?',
        'group'=>'comicpic_author',
        'order'=>5,
        'help'=>'You are required accept the terms and conditions before proceeding. Make sure you read the terms and conditions',
        'editDescription'=>'Required',
        'value'=>[''],
        'options'=>['yes'=>'yes'],//Note that the label will not be printed since their is only one item here
        'rules' => 'required',
        'isWriteOnly'=>true,//Field should be display when viewing form 

        ]);

        //Example of Fieldset
        $fieldset=Fieldset::make([
        'name'=>'comicpic_fset',
        'label'=>'Check',
        'group'=>'comicpic_author',
        'order'=>5,
        ]);
        $fieldset->legend='Test fieldset';

        // Lets add fields to the fieldset
        $fieldset->addField($test1);
        $fieldset->addField($test2);
        $fieldset->addField($test3);

        //Example of Group
        $group=Group::make(['name'=>'comicpic_author2','label'=>'Test group','order'=>-3.1]);

        //Create a field that will belong to the group
        $textarea=Field::make([   'type'=>Field::TEXTAREA,
        'name'=>'comicpic_textarea',
        'label'=>'Help description',
        'group'=>'comicpic_author2',
        'order'=>-225,
        'help'=>'Help text',
        'placeholder'=>'Enter author name',
        'class'=>'',
        'unit'=>'',
        'value'=>'Sample text area',
        'rules' => 'nullable',

        ]);

        // Now lets add all form items to the form 
        $form->addField($fieldset);
        $form->addField($group);
        $form->addField($textarea);
        
```


## Handling form request
An example in a controller:
```php
    public function update(Request $request)
    {
        $form = new Form($pack, $tag,'edit');

        // Process any field that is not from fieldable, e.g added with $form->addField() or rendered directly to the form. For example say a 'name' field was added, then we can process thus.
        $rules = ['name' => 'required|string|max:255',];
        $this->validate($request, $rules); // 
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;

        //Note that we can alternatively add the above field back into the form so that we can validate it together with those from fieldables as below.

        // Now process extrinsic fields, i.e from fieldables
        $form->getValidator($request->all())->validate();
        $form->process($request);

        $user->save();
        ...
    }
```

## User profile forms
The user profile page automates the display and editing of fieldables registered to the 'user_settings' pack. You can easily create a new form in this pack by adding Fieldable/s to it with a unique tag. if the tag already exists then the Fieldable/s will be added to the existing form instead. The defined tags include 'personal' and 'address'.

## Rendering form
### Form
The simplest way to render a form is to include the form view in blade:

```blade.php
// index
@include('laradmin::form.index_form',['form'=>$form])

// edit
@include('laradmin::form.edit_form',['form'=>$form])
```

### Fields
If you want more control on how the form is rendered you can call the fields view yourself. Here is an example for the edit page of the form:
```html
<form method="@if(str_is(strtolower($form->method),'get')){{'GET'}}@else{{'POST'}}@endif" action="{{$form->getEditLink()}}"
        @if($form->hasImageField($form->getFields())) enctype="multipart/form-data" @endif >
    
    @if(!in_array(strtolower($form->method),['get','post']))
        {{ method_field($form->method) }}
    @endif
    ...
    @foreach($form->getGroupedFields() as $group_name=> $fields)
        <div class="group">
            @if($form->getGroup($group_name))
                <h6 class="label label-warning ">{{$form->getGroup($group_name)->label??ucfirst($group_name)}}</h6>
                <span class="description">{{$form->getGroup($group_name)->editDescription}}</span>
            @endif
            @component('laradmin::form.edit_fields',['fields'=>$fields])
            @endcomponent
        </div>
    @endforeach
    ...
    <button type="submit" class="btn btn-primary">
        Update
    </button>
    @if($form->editBottomMessage)<p class=" fainted-08"><small>{{$form->editBottomMessage}}</small></p>@endif
    @includeIf($form->getEditBottom())
</form>
```
You should see view,  'laradmin::form.edit_form' for more details, and see 'laradmin::form.index_form'  for the index page.
### Field
If you even want more control, you can render each Field yourself. See views 'laradmin::form.index_form' and 'laradmin.form.index_fields' for how to do this for index page of the form and similarly for the edit page.

## Fieldable
Fieldables are means of providing fields to forms. To create a Fieldable create a class that implements \BethelChika\Laradmin\Form\Contracts\Fieldable. Below is an example implementation:

```php
namespace BethelChika\Comicpic\Form;
use BethelChika\Laradmin\Form\Contracts\Fieldable;
use BethelChika\Laradmin\Form\Field;
use BethelChika\Laradmin\Form\Group;
use Illuminate\Support\Facades\Auth;

class ComicpicFieldable implements Fieldable{
    /**
     * @inheritdoc
     */
    public function handle($pack,$tag,Field $field){
        $user=Auth::user();
        switch($field->name){
            case 'country':
                $user->country=$field->value;
                $user->save();
                break;
            case ...

        }
        return 1;
    }
      /**
     * @inheritdoc
     */
    public function all($pack,$tag,$mode){
        $group1=Group::make(['name'=>'comicpic_location','label'=>'Comicpic location','order'=>10]);
        $f1=Field::make([
            'name'=>'country',
            'type'=>'select',
            'value'=>Auth::user()->country,
            'label'=>'Country',
            'order'=>5,
            'rules'=>'required:min:2',
            'options'=>__( 'laradmin::list_of_countries'),
        ]);    
        return collect([$group1,$f1]);
    }
}
```

The all() method of fieldables should return a collection Field/s which will be display the calling form. It can is called with three arguments: $pack, $tag and $mode. The $pack and the $tag identifies the current form calling the Fiedable and the $mode which has two possible values {'index','edit'} is used to tell if the form is currently in edit of index mode. The $mode can be used to return different fields for when editing the form and when displaying it in the index mode.
### Register Fieldable
To register a fieldable you will need a pack and a tag. A pack can have many forms. In each pack forms are identified by a tag. So a form is identified by a pack and a tag combination. You can register a fieldable to a form by registering it with the forms pack and tag thus:
```php
$formmanager->registerFieldable($pack,$tag,ComicpicFieldable::class);
```
A place to register fieldables is in the boot method of a service provider. A fielable can be registered to any number of $pack|$tag combinations. If you register a Fieldable more than once, you can check the pack and tag in the callback methods to know which registration is currently calling and respond appropriately. For example example you can return different fields in the all() method:
```php
...
public function all($pack,$tag,$mode){
    if(str_is($pack,'user_settings') and str_is($tag,'profile') ){
        return Field::make(['name'=>'comicpic_author','type'=>'text','label'=>'Author']);
    }elseif((str_is($pack,'account') and str_is($tag,'likes') )){
        return Field::make(['name'=>'comicpic_likes','type'=>'text','label'=>'Likes']);
    }
    
}
``` 
In this way one Fieldable can be used as many times as required.


### Handling Fieldables
The handle() methods are called once for each field the Fieldable has supplied  during the edit call of a form.  If the field is an image then the value is a an instance of \Illuminate\Http\UploadedFile which should be handled Laravel style or using Intervention Image. You should move the file to an apprioprate loaction and make sure that the temporary file is deleted.


### Unregistering Fieldables
```php
$formManager->unregisterFieldable('comicpic');
$formManager->unregisterFieldable('comicpic','settings');
```
The first line deletes all the Fieldables for all forms in the pack of 'comicpic' while the second only the deletes the Fieldable for a form in the pack with a tag of 'settings'. 

## Define packs and tags
Packs and tags are actually only defined when initialising a form. E.g.
```php
$form=new Form('comicpic','settings',$mode);
```
declares that the $form owns all the everything registered to pack='comicpic' and tag='settings'.
The following defined ta



## Auto Form
AutoForm enable creation of automatic forms such that the display and editing of the form is done automatically. Autoforms are Forms that extends the \BethelChika\Laradmin\Form\Contracts\Autoform abstract class. AutoForms are also Fieldables as well as forms and so has all the relevant methods with some additions, like gate() etc.
### Register auto form
Registering auto forms is similar to registering fieldables:
```php
$formmanager->registerAutoforms($pack,$tag,ComicpicAutoform::class);
```
All Autoforms with the same pack are listed together in a tab menu for easy navigation through the forms. You can add any number of forms into the pack by varying the tag e.g:
```php
$formmanager->registerAutoforms('comicpic','settings',SettingsAutoform::class);
$formmanager->registerAutoforms('comicpic','author',AuthorAutoform::class);
```
Note that registering to an already existing tag will overwrite.


### Using auto forms
To use an Autoform you should create a  url to it thus:
```php
$url=$formmanager->getAutoformLink($pack,$tag);

```
Or in blade
```php
<a href="{{$form->getLink()}}">Open form</a>
```
The above will open the display of the form. If you want to open the form in editing mode you should  use the following:
```php
$url=$formmanager->getAutoformEditLink($pack,$tag);

```
Or in blade
```php
<a href="{{$form->getEditLink()}}">Open form</a>
```

## Fieldables for Autoform
Fieldables registered to an Autoform will be automatically loaded and in the display and editing of the form. To register a Fieldable to an Autoform you will need to first find the pack of and tag of the form. E.g to register a Fieldable to an Autoform with pack of 'comicpic', and tag, 'settings' you should do the following in the boot method of a service provider:
```php
$formManager->registerAutoform('comicpic','settings',MyFieldable::class);
```
## Manual manipulation of autoforms
Since Autoform extends Form it can also be handled like any other form, e.g display and edit it with a custom template. Autoform is also a Fieldable and there can be registered as a fieldable to be processed in other forms. 

## Unregistering AutoForms
```php
$formManager->unregisterAutoform('comicpic');
$formManager->unregisterAutoform('comicpic','settings');
```
The first line deletes all the forms in the pack of 'comicpic' while the second only the deletes the form with a tag of 'settings'. Note that Fieldables registered to the forms are not touched; they should be deleted like normal Fieldables.