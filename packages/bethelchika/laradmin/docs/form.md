# Form
##Creating custom form 
Create form simply thus:
```php

use \BethelChika\Laradmin\Form\Form;
class MyFormController{
    $form=new Form($pack,$tag);
}
```

You can add fields to the form by calling the addField method. You either add Field, Fieldset. You can also add  Group as a field.  A field is the basic control of the form. The fields can instead be added to a Fieldset with the fieldset then added to the form. A groupe is different although it is added as a field; it is not a control rather a grouping of fields and fieldsets. Each fieldset should have group name which define which group it belongs to. Fields are displayed accorging to their groups.
 Add fields to the form:
 ```php
 ...
$field=FormItem::make([   'type'=>'text',
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
Group and Fieldset can be similarly be added.
## Handling form request

## Rendering form

## Fieldable
Fieldables are means of providing fields to forms. To create a Fieldable create a class that implements \BethelChika\Laradmin\Form\Contracts\Fieldable. 
### Register Fieldable
To register a fieldable you will need a pack and a tag. A pack can have many forms. In each pack forms are identified by a tag. So a form is identified by a pack and a tag. You can register a fieldable to a form by registering it with the forms pack and tag thus:
```php
$formmanager->registerFieldable($pack,$tag,ComicpicFieldable::class);
```
A place to register fieldables is the boot method of a service provider.

## Auto Form
AutoForm enable creation of automatic forms the display and editing of the form is done automatically. Autoforms are Forms that extends the \BethelChika\Laradmin\Form\Contracts\Autoform abstract class. AutoForms are also Fieldables as well as forms and fo has all the relevant methods with some additions like gate() etc.
### Register auto form
Registering auto forms is similar to registering fieldables:
```php
$formmanager->registerAutoforms($pack,$tag,ComicpicFieldable::class);
```
### Using auto forms
To use an autofrom you should create a  url to it thus:
```php
$formmanager->getAutoformLink($pack,$tag);
```