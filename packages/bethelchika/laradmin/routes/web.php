<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/','HomeController@index')->name('home');

//Route::get('/home', 'HomeController@index');

//Auth::routes();
//Auth::routes();
Route::group(['middleware' => ['web']], function () {

    $LCR_LOCAL="\\BethelChika\\Laradmin\\Http\\Controllers\\";//Laradmin Controller Roots
    $LCR_EXT="\\App\\Http\\Controllers\\Laradmin\\";// User own implementation of Laradmin controller roots
    
    
    // Laradmins User Rgistration (THE output of Auth::routes() should be modified to remove the link to registration)
    $LCR=class_exists($LCR_EXT.'User\RegisterController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/register', $LCR.'User\RegisterController@showRegistrationForm')->name('user-register');
    Route::post('/u/register', $LCR.'User\RegisterController@register');
    

    /**
    * Laradmin routes
    **/
    // Userprofile routes
    $LCR=class_exists($LCR_EXT.'User\UserProfileController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/index', $LCR.'User\UserProfileController@index')->name('user-home');
    Route::get('/u/profile/{form_pack?}/{form_tag?}', $LCR.'User\UserProfileController@profile')->name('user-profile');
    Route::get('/u/edit_profile/{form_pack}/{form_tag}', $LCR.'User\UserProfileController@edit')->name('user-profile-edit');
    Route::put('/u/edit_profile/{form_pack}/{form_tag}', $LCR.'User\UserProfileController@update');
    Route::get('/u/settings', $LCR.'User\UserProfileController@settings')->name('user-settings');
    Route::get('/u/security', $LCR.'User\UserProfileController@security')->name('user-security');
    Route::get('/u/edit-password', $LCR.'User\UserProfileController@editPassword')->name('user-edit-password');
    Route::put('/u/edit-password', $LCR.'User\UserProfileController@updatePassword');
 
    Route::get('/u/send-email-confirmation/',$LCR.'User\UserProfileController@sendEmailConfirmation')->name('send-email-confirmation');
    Route::get('/u/email-confirmation/{email}/{key}',$LCR.'User\UserProfileController@emailConfirmation')->name('email-confirmation');

    Route::get('/u/account-control',$LCR.'User\UserProfileController@accountControl')->name('user-account-control');
    Route::get('/u/delete',$LCR.'User\UserProfileController@initiateSelfDelete')->name('user-self-delete');
    Route::get('/u/cancel-delete',$LCR.'User\UserProfileController@cancelSelfDelete')->name('user-self-delete-cancel');
    Route::get('/u/deactivate',$LCR.'User\UserProfileController@selfDeactivate')->name('user-self-deactivate');
    Route::get('/u/reactivate',$LCR.'User\UserProfileController@selfReactivate')->name('user-self-reactivate');
    //Route::get('/u/plugin-settings',$LCR.'User\UserProfileController@pluginSettings')->name('user-plugin-settings');
    Route::get('/u/alerts',$LCR.'User\UserProfileController@userAlerts')->name('user-alerts');

    //deletes theese////////////////////////////////////////
    Route::get('/u/form-create/',$LCR.'User\UserProfileController@formCreate')->name('form-create');
    Route::put('/u/form-create/',$LCR.'User\UserProfileController@updateForm');
    /////////////////////////////////////////////////////////

    // AutoForm
    $LCR=class_exists($LCR_EXT.'User\AutoformController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/autoform/{pack}/{tag}/index',$LCR.'User\AutoformController@index')->name('user-autoform');
    Route::get('/u/autoform/{pack}/{tag}/edit',$LCR.'User\AutoformController@edit')->name('user-autoform-edit');
    Route::put('/u/autoform/{pack}/{tag}/edit',$LCR.'User\AutoformController@process');


    // Notifications
    $LCR=class_exists($LCR_EXT.'User\NotificationController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/notofication/index',$LCR.'User\NotificationController@index')->name('user-notification-index');
    Route::post('/u/notofication/mark-as-ajax',$LCR.'User\NotificationController@markAsAjax')->name('user-notification-mark-ajax');
    Route::delete('/u/notofication/{notofication}',$LCR.'User\NotificationController@destroy')->name('user-notification-delete');

    //User Message
    $LCR=class_exists($LCR_EXT.'User\UserMessageController')?$LCR_EXT:$LCR_LOCAL;
    Route::delete('/user-message/' ,$LCR.'User\UserMessageController@destroys')->name('user-message-deletes');
    //Resource-------------------------------
    Route::post ('/user-message',$LCR.'User\UserMessageController@store')->name('user-message-store');
    Route::get('/user-message' ,$LCR.'User\UserMessageController@index')->name('user-message-index');
    Route::get('/user-message/create' ,$LCR.'User\UserMessageController@create')->name('user-message-create');
    Route::delete('/user-message/{message}' ,$LCR.'User\UserMessageController@destroy')->name('user-message-delete');
    Route::get('/user-message/{message}',$LCR.'User\UserMessageController@show')->name('user-message-show');
    Route::put('/user-message/{message}' ,$LCR.'User\UserMessageController@update')->name('user-message-update');
    Route::get('/user-message/{message}/edit ',$LCR.'User\UserMessageController@edit')->name('user-message-edit');
    //-----------------------------------------------
    //Route::resource('/cp/message',$LCR.'CP\UserMessageController');
    Route::put('/user-message/mark-as/ajax' ,$LCR.'User\UserMessageController@markAsAjax')->name('user-message-mark-ajax');
    Route::post('/user-message/reply',$LCR.'User\UserMessageController@reply')->name('user-message-reply');

    //Contact us
    Route::get('/contact-us/create' ,$LCR.'User\ContactUsUserMessageController@create')->name('contact-us-create');
    Route::post ('/contact-us',$LCR.'User\ContactUsUserMessageController@store')->name('contact-us-store');


    //SocialUser
    $LCR=class_exists($LCR_EXT.'User\SocialUserController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/social-user', $LCR.'User\SocialUserController@index')->name('social-user');
    Route::get('/u/social-user/callout/{provider}/{callType?}', $LCR.'User\SocialUserController@redirectToProvider')->name('social-user-callout');
    Route::get('/u/social-user/callback/{provider}', $LCR.'User\SocialUserController@handleProviderCallback')->name('social-user-callback');
    Route::delete('/u/social-user/unlink/{socialUser}', $LCR.'User\SocialUserController@unlinkSocialUser')->name('social-user-delete');
    Route::get('/u/social-user-external', $LCR.'User\SocialUserController@externalAccounts')->name('social-user-external');
    
    $LCR=class_exists($LCR_EXT.'User\SocialUserLinkEmailController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/social-user/link-email', $LCR.'User\SocialUserLinkEmailController@index')->name('social-user-link-email');
    Route::post('/u/social-user/link-email', $LCR.'User\SocialUserLinkEmailController@store')->name('social-user-link-email-create');
    Route::delete('/u/social-user/link-email/{socialUser}', $LCR.'User\SocialUserLinkEmailController@destroy')->name('social-user-link-email-delete');
    Route::get('/u/social-user/link-email/confirmation/{socialUser}/{key}', $LCR.'User\SocialUserLinkEmailController@linkEmailConfirmation')->name('social-user-link-email-confirm');
    Route::get('/u/social-user/link-email/resend-confirmation/{socialUser}', $LCR.'User\SocialUserLinkEmailController@resendConfirmationEmail')->name('social-user-link-email-confirm-resend');
    Route::put('/u/social-user/link-email/set-primary/{socialUser}', $LCR.'User\SocialUserLinkEmailController@setPrimaryEmail')->name('social-user-link-email-set-primary');

    // Feeds
    $LCR=class_exists($LCR_EXT.'User\FeedController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/feed', $LCR.'User\FeedController@fetch')->name('user-feed');
    
    
    // Reauthentication
    Route::middleware(['auth'])->group(function ( ) use( $LCR,$LCR_EXT,$LCR_LOCAL) {
        //$this->middleware('auth');
        $LCR=class_exists($LCR_EXT.'User\UserProfileController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/u/re-auth/', $LCR.'User\UserProfileController@reAuthIndex')->name('re-auth');
        Route::post('/u/re-auth/', $LCR.'User\UserProfileController@reAuth');
        
        $LCR=class_exists($LCR_EXT.'User\SocialUserController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/u/re-auth-social-user/{authSocialUser}', $LCR.'User\SocialUserController@reAuthWithSocialUser')->name('re-auth-social-user');

    });


    // WP (Wordpress)
    Route::prefix(config('laradmin.page_url_prefix'))->group(function () use( $LCR_EXT,$LCR_LOCAL){
        $LCR=class_exists($LCR_EXT.'User\WPController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('{slug}', $LCR.'User\WPController@page')->name('page');
    });

    Route::prefix('post')->group(function () use( $LCR_EXT,$LCR_LOCAL){
        $LCR=class_exists($LCR_EXT.'User\WPController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('{slug}', $LCR.'User\WPController@post')->name('post');
    });
    Route::get(config('laradmin.wp_rpath').'/{slug}',function(){
        //This should never execute as it is installed in config('laradmin.wp_rpath'), Wordpress should already grab the request right?
        return 'Problem with WordPress';
    })->name('wp');
    





    //Admin pages ****************************************************************
    //****************************************************************************
    Route::prefix('cp')->group(function () use( $LCR_EXT,$LCR_LOCAL){
        $LCR=class_exists($LCR_EXT.'CP\ControlPanelController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/index', $LCR.'CP\ControlPanelController@index')->name('cp');
        Route::get('/help', $LCR.'CP\ControlPanelController@help')->name('cp-help');

        // Users
        $LCR=class_exists($LCR_EXT.'CP\UserController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/users', $LCR.'CP\UserController@index')->name('cp-users');
        Route::get('/user/{user}/show/', $LCR.'CP\UserController@show')->name('cp-user');
        Route::get('/user/{user}/edit/', $LCR.'CP\UserController@edit')->name('cp-user-edit');
        Route::put('/user/{user}/update/', $LCR.'CP\UserController@update')->name('cp-user-update');

        Route::get('/user/create/',$LCR.'CP\UserController@create')->name('cp-user-create');
        Route::post('/user/create/',$LCR.'CP\UserController@store')->name('cp-user-store');

        Route::delete('/user/{user}/',$LCR.'CP\UserController@destroy')->name('cp-user-delete');
        Route::delete('/users/',$LCR.'CP\UserController@destroys')->name('cp-users-delete');

        Route::get('/send-email-confirmation/{user}',$LCR.'CP\UserController@sendEmailConfirmation')->name('cp-send-email-confirmation');
        Route::get('/email-confirmation/{user}/',$LCR.'CP\UserController@emailConfirmation')->name('cp-email-confirmation');

        Route::get('/user/{user}/disable',$LCR.'CP\UserController@disableUser')->name('cp-user-disable');
        Route::get('/user/{user}/enable',$LCR.'CP\UserController@enableUser')->name('cp-user-enable');

        //User group map
        $LCR=class_exists($LCR_EXT.'CP\UserGroupMapController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/user/{user}/group-map/edits/',$LCR.'CP\UserGroupMapController@edits')->name('cp-user-group-map-edits');
        Route::put('/user/{user}/group-map/updates/',$LCR.'CP\UserGroupMapController@updates')->name('cp-user-group-map-updates');

        // User groups
        $LCR=class_exists($LCR_EXT.'CP\UserGroupController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/user-groups/',$LCR.'CP\UserGroupController@index')->name('cp-user-groups');
        Route::get('/user-group/{userGroup}/show',$LCR.'CP\UserGroupController@show')->name('cp-user-group');
        Route::get('/user-group/{userGroup}/edit',$LCR.'CP\UserGroupController@edit')->name('cp-user-group-edit');
        Route::put('/user-group/{userGroup}/',$LCR.'CP\UserGroupController@update')->name('cp-user-group-update');
        Route::get('/user-group/create/',$LCR.'CP\UserGroupController@create')->name('cp-user-group-create');
        Route::post('/user-group/create/',$LCR.'CP\UserGroupController@store')->name('cp-user-group-store');
        Route::delete('/user-group/{userGroup}/',$LCR.'CP\UserGroupController@destroy')->name('cp-user-group-delete');
        Route::delete('/user-groups/',$LCR.'CP\UserGroupController@destroys')->name('cp-user-groups-delete');

        // Sources
        $LCR=class_exists($LCR_EXT.'CP\SourceController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/source/create',$LCR.'CP\SourceController@create')->name('cp-source-create');
        Route::post('/source/create',$LCR.'CP\SourceController@store');
        //Route::get('/sources/',$LCR.'CP\SourceController@index')->name('cp-sources');
        ////Types of sources
        Route::get('/source/types',$LCR.'CP\SourceController@types')->name('cp-source-types');
        Route::get('/source/types/table',$LCR.'CP\SourceController@tables')->name('cp-source-type-table');
        Route::get('/source/types/route',$LCR.'CP\SourceController@routes')->name('cp-source-type-route');
        Route::get('/source/types/route_prefix',$LCR.'CP\SourceController@routePrefixes')->name('cp-source-type-route_prefix');
        Route::get('/source/types/page',$LCR.'CP\SourceController@pages')->name('cp-source-type-page');
        Route::get('/source/types/{type}',$LCR.'CP\SourceController@type')->name('cp-source-type');//For any source type without its own specific route definition
        ////individual source
        Route::get('/source/show/table/{name}',$LCR.'CP\SourceController@showTable')->name('cp-source-show-table');
        Route::get('/source/show/route/',$LCR.'CP\SourceController@showRoute')->name('cp-source-show-route');
        Route::get('/source/show/route_prefix/',$LCR.'CP\SourceController@showRoutePrefix')->name('cp-source-show-route_prefix');
        Route::get('/source/show/page/{id}',$LCR.'CP\SourceController@showPage')->name('cp-source-show-page');
        Route::get('/source/show/{id}',$LCR.'CP\SourceController@show')->name('cp-source-show');
        // updating source
        Route::get('/source/edit/{source}',$LCR.'CP\SourceController@edit')->name('cp-source-edit');
        Route::put('/source/edit/{source}',$LCR.'CP\SourceController@update');
        //Delete source
        Route::delete('/source/edit/{source}',$LCR.'CP\SourceController@destroy');
        
        // Permission ___________
        $LCR=class_exists($LCR_EXT.'CP\PermissionController')?$LCR_EXT:$LCR_LOCAL;
        Route::put('/source/permissions',$LCR.'CP\PermissionController@update')->name('cp-source-permission-update');
        Route::get('/source/permissions/search-users',$LCR.'CP\PermissionController@searchUsers')->name('cp-source-permission-search-users');
        Route::post('/source/permissions/store-permission',$LCR.'CP\PermissionController@store')->name('cp-source-permission-store');
        Route::delete('/source/permissions/delete-permission',$LCR.'CP\PermissionController@destroy')->name('cp-source-permission-delete');

        //CP User Message____________
        $LCR=class_exists($LCR_EXT.'CP\UserMessageController')?$LCR_EXT:$LCR_LOCAL;
        Route::delete('/user-message/' ,$LCR.'CP\UserMessageController@destroys')->name('cp-user-message-deletes');
        //Resource-------------------------------
        Route::post ('/user-message',$LCR.'CP\UserMessageController@store')->name('cp-user-message-store');
        Route::get('/user-message' ,$LCR.'CP\UserMessageController@index')->name('cp-user-message-index');
        Route::get('/user-message/create' ,$LCR.'CP\UserMessageController@create')->name('cp-user-message-create');
        Route::delete('/user-message/{message}' ,$LCR.'CP\UserMessageController@destroy')->name('cp-user-message-delete');
        Route::get('/user-message/{message}',$LCR.'CP\UserMessageController@show')->name('cp-user-message-show');
        Route::put('/user-message/{message}' ,$LCR.'CP\UserMessageController@update')->name('cp-user-message-update');
        Route::get('/user-message/{message}/edit ',$LCR.'CP\UserMessageController@edit')->name('cp-user-message-edit');
        //-----------------------------------------------
        //Route::resource('/cp/message',$LCR.'CP\UserMessageController');
        Route::put('/user-message/mark-as/ajax' ,$LCR.'CP\UserMessageController@markAsAjax')->name('cp-user-message-mark-ajax');
        Route::post('/user-message/reply',$LCR.'CP\UserMessageController@reply')->name('cp-user-message-reply');

        // Notifications
        $LCR=class_exists($LCR_EXT.'CP\NotificationController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/notification/index',$LCR.'CP\NotificationController@index')->name('cp-notification-index');
        Route::post('/notification/mark-as-ajax',$LCR.'CP\NotificationController@markAsAjax')->name('cp-notification-mark-ajax');
        Route::delete('/notification/{notification}',$LCR.'CP\NotificationController@destroy')->name('cp-notification-delete');

        // General seetings
        $LCR=class_exists($LCR_EXT.'CP\SettingsController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/settings/edit',$LCR.'CP\SettingsController@edit')->name('cp-settings-edit');
        Route::get('/settings/edit/storage-link',$LCR.'CP\SettingsController@storageLink')->name('cp-settings-storage-link');


        // Plugins
        $LCR=class_exists($LCR_EXT.'CP\PluginAdminController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/plugins/index',$LCR.'CP\PluginAdminController@index')->name('cp-plugins');
        Route::get('/plugin',$LCR.'CP\PluginAdminController@show')->name('cp-plugin');
        Route::post('/plugin',$LCR.'CP\PluginAdminController@install');
        Route::put('/plugin/enable',$LCR.'CP\PluginAdminController@enable')->name('cp-plugin-enable');
        Route::put('/plugin/disable',$LCR.'CP\PluginAdminController@disable')->name('cp-plugin-disable');
        Route::get('/plugin/publish',$LCR.'CP\PluginAdminController@publishing')->name('cp-plugin-publish');
        Route::put('/plugin/publish',$LCR.'CP\PluginAdminController@publish');//->name('cp-plugin-publish');
        Route::delete('/plugin',$LCR.'CP\PluginAdminController@uninstall');
        Route::get('/plugin/update',$LCR.'CP\PluginAdminController@updating')->name('cp-plugin-update');
        Route::put('/plugin/update',$LCR.'CP\PluginAdminController@update');
        Route::delete('/plugin/update',$LCR.'CP\PluginAdminController@updateCancel');
    });

     
});


