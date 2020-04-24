@extends('laradmin::cp.layouts.app')
@section('page-top')
    <h1 class='page-title'>{{$pageTitle??' '}}</h1>
@endsection

@section('content')

  <h2><a name="permission">//</a> Introdunction to authorisation/permission</h2>
  <p>There are many ways to authorise access in Laradmin.
      Out of the box the best are through <i class="label label-warning">URL</i>, <i class="label label-warning">route</i> and <i class="label label-warning">URL prefix</i>, which are implemented
      using the <i class="label label-warning">pre-authorise</i> middleware. Any route or controller that wants to use the 
      the <i>URL</i>, <i>route</i> and <i>URL prefix</i> permissions must use the <i class="label label-warning">pre-authorise</i> middleware.
</p>   

<p> Specific permission are implemeted through models, tables, pages and files.
    Out of the box, checking for permissions added to models and tables mostly done in the user profile controllers and in few control controllers. It is left for a programmer to ensure that the 
    permissions are checked if the models and pages are to be preferred for permission
    instead of using the <i>URL</i>, <i>route</i> and <i>URL prefix</i> which are always checked in the
    <i class="label label-warning">pre-authorise</i> middleware. 
</p>

<p>Permission applied to pages will always be check for thr correponding Wordpress pages. </p>
<div class="alert alert-warning"> <i class="fas fa-exclamation-triangle"></i> Note: if there is at least one permission <i>READ</i> entry for a page, the page will require login for access! i.e the entry marks the page as protected.</div>
<p> Permission for files are left for a programmer to implement. For example, it could be used to authorise downloadable files/digital contents.</p>

<h3>Applying permission</h3>
To add a permission:
<ol>
    <li>From the Control panel sidebar, click the type of source you would like to apply to the permission to</li>
    <li>Click on the source from the list of sources</li>
    <li>Th resulting page presents a window for entering  permissions</li>
    <li>From the right side of the permission window click the plus symbole to open the 'Add user or group panel' </li>
    <li>Use the resulting panel to search and add a user(s) or a group(s) to the permission box and click close when you finish</li>
    <li> Now click of the added user or group in the permission box and use the checkboxes (Create,Read,Update and Delete) below the permission box to apply the required permission. 
    </li>
    <li> Click 'Apply permission' button to commit the changes.</li>
</ol>

<h3>Gates</h3>
<p>
The Control panel has a gate called <i class="label label-warning">cp</i> 
which checks 'read' permission on the '/cp' route prefix.
</p>


                
 
@endsection
