<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/


Route::group(['middleware' => ['web']], function () {

    $LCR="\\BethelChika\\Laradmin\\Http\\Controllers\\";//Laradmin Controller Roots
   
    //Admin pages ****************************************************************
    //****************************************************************************
    Route::prefix('cp')->group(function () use( $LCR){
        //Route::get('/index', $LCR.'CP\ControlPanelController@index')->name('cp');
        Route::get('/help', $LCR.'CP\ControlPanelController@help')->name('cp-help');

        // Users
        Route::get('/users', $LCR.'CP\UserController@index')->name('cp-users');
        Route::get('/user/{user}/show/', $LCR.'CP\UserController@show')->name('cp-user');
        Route::get('/user/{user}/edit/', $LCR.'CP\UserController@edit')->name('cp-user-edit');
        Route::put('/user/{user}/update/', $LCR.'CP\UserController@update')->name('cp-user-update');

        Route::get('/user/create/',$LCR.'CP\UserController@create')->name('cp-user-create');
        Route::post('/user/create/',$LCR.'CP\UserController@store')->name('cp-user-store');

        Route::delete('/user/{user}/',$LCR.'CP\UserController@destroy')->name('cp-user-delete');
        Route::delete('/users/',$LCR.'CP\UserController@destroys')->name('cp-users-delete');

        Route::get('/email-confirmation/send/{user}',$LCR.'CP\UserController@sendEmailConfirmation')->name('cp-send-email-confirmation');
        Route::get('/email-confirmation/confirm/{user}/',$LCR.'CP\UserController@emailConfirmation')->name('cp-email-confirmation');

        Route::get('/user/{user}/disable',$LCR.'CP\UserController@disableUser')->name('cp-user-disable');
        Route::get('/user/{user}/enable',$LCR.'CP\UserController@enableUser')->name('cp-user-enable');

        //User group map
        Route::get('/user/{user}/group-map/edits/',$LCR.'CP\UserGroupMapController@edits')->name('cp-user-group-map-edits');
        Route::put('/user/{user}/group-map/updates/',$LCR.'CP\UserGroupMapController@updates')->name('cp-user-group-map-updates');

        // User groups
        Route::get('/user-groups/',$LCR.'CP\UserGroupController@index')->name('cp-user-groups');
        Route::get('/user-group/{userGroup}/show',$LCR.'CP\UserGroupController@show')->name('cp-user-group');
        Route::get('/user-group/{userGroup}/edit',$LCR.'CP\UserGroupController@edit')->name('cp-user-group-edit');
        Route::put('/user-group/{userGroup}/',$LCR.'CP\UserGroupController@update')->name('cp-user-group-update');
        Route::get('/user-group/create/',$LCR.'CP\UserGroupController@create')->name('cp-user-group-create');
        Route::post('/user-group/create/',$LCR.'CP\UserGroupController@store')->name('cp-user-group-store');
        Route::delete('/user-group/{userGroup}/',$LCR.'CP\UserGroupController@destroy')->name('cp-user-group-delete');
        Route::delete('/user-groups/',$LCR.'CP\UserGroupController@destroys')->name('cp-user-groups-delete');

        // Sources
        Route::get('/source/types/create',$LCR.'CP\SourceController@create')->name('cp-source-create');
        Route::post('/source/create',$LCR.'CP\SourceController@store');

        ////Types of sources
        Route::get('/source/types/',$LCR.'CP\SourceController@types')->name('cp-source-types');

        Route::get('/source/types/table',$LCR.'CP\SourceController@tables')->name('cp-source-type-table');
        Route::get('/source/types/table/show/{name}',$LCR.'CP\SourceController@showTable')->name('cp-source-show-table');

        Route::get('/source/types/route',$LCR.'CP\SourceController@routes')->name('cp-source-type-route');
        Route::get('/source/types/route/show',$LCR.'CP\SourceController@showRoute')->name('cp-source-show-route');

        Route::get('/source/types/route_prefix',$LCR.'CP\SourceController@routePrefixes')->name('cp-source-type-route_prefix');
        Route::get('/source/types/route_prefix/show',$LCR.'CP\SourceController@showRoutePrefix')->name('cp-source-show-route_prefix');

        Route::get('/source/types/page',$LCR.'CP\SourceController@pages')->name('cp-source-type-page');
        Route::get('/source/types/page/show/{id}',$LCR.'CP\SourceController@showPage')->name('cp-source-show-page');

        Route::get('/source/types/{type}',$LCR.'CP\SourceController@type')->name('cp-source-type');//For any source type without its own specific route definition
        Route::get('/source/types/{type}/show/{id}',$LCR.'CP\SourceController@show')->name('cp-source-show');

        // updating source
        Route::get('/source/types/{type}/edit/{source}',$LCR.'CP\SourceController@edit')->name('cp-source-edit');
        Route::put('/source/types/{type}/edit/{source}',$LCR.'CP\SourceController@update');
        //Delete source
        Route::delete('/source/edit/{source}',$LCR.'CP\SourceController@destroy');

        // Permission ___________

        Route::put('/source/permissions',$LCR.'CP\PermissionController@update')->name('cp-source-permission-update');
        Route::get('/source/permissions/search-users',$LCR.'CP\PermissionController@searchUsers')->name('cp-source-permission-search-users');
        Route::post('/source/permissions/store-permission',$LCR.'CP\PermissionController@store')->name('cp-source-permission-store');
        Route::delete('/source/permissions/delete-permission',$LCR.'CP\PermissionController@destroy')->name('cp-source-permission-delete');

        //CP User Message____________
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

        Route::put('/user-message/mark-as/ajax' ,$LCR.'CP\UserMessageController@markAsAjax')->name('cp-user-message-mark-ajax');
        Route::post('/user-message/reply',$LCR.'CP\UserMessageController@reply')->name('cp-user-message-reply');

        // Notifications
        Route::get('/notification/index',$LCR.'CP\NotificationController@index')->name('cp-notification-index');
        Route::post('/notification/mark-as-ajax',$LCR.'CP\NotificationController@markAsAjax')->name('cp-notification-mark-ajax');
        Route::delete('/notification/{notification}',$LCR.'CP\NotificationController@destroy')->name('cp-notification-delete');

        // Settings
        Route::get('/settings/{form_pack?}/{form_tag?}',$LCR.'CP\SettingsController@index')->name('cp-settings');

        // Post Installation settings
        Route::get('/post_install',$LCR.'CP\SettingsController@postInstall')->name('cp-post-install');
        Route::put('/post_install/wpitems',$LCR.'CP\SettingsController@wpInstallItems')->name('cp-post-install-wpitems');
        Route::get('/post_install/storage-link',$LCR.'CP\SettingsController@storageLink')->name('cp-post-install-storage-link');

        // Plugins
        Route::get('/plugins',$LCR.'CP\PluginAdminController@index')->name('cp-plugins');
        Route::get('/plugins/show',$LCR.'CP\PluginAdminController@show')->name('cp-plugin');
        Route::post('/plugins/install',$LCR.'CP\PluginAdminController@install')->name('cp-plugin-install');
        Route::put('/plugins/enable',$LCR.'CP\PluginAdminController@enable')->name('cp-plugin-enable');
        Route::put('/plugins/disable',$LCR.'CP\PluginAdminController@disable')->name('cp-plugin-disable');
        Route::get('/plugins/publish',$LCR.'CP\PluginAdminController@publishing')->name('cp-plugin-publish');
        Route::put('/plugins/publish',$LCR.'CP\PluginAdminController@publish');//->name('cp-plugin-publish');
        Route::delete('/plugins',$LCR.'CP\PluginAdminController@uninstall')->name('cp-plugin-uninstall');
        Route::get('/plugins/update',$LCR.'CP\PluginAdminController@updating')->name('cp-plugin-update');
        Route::put('/plugins/update',$LCR.'CP\PluginAdminController@update');
        Route::delete('/plugins/update',$LCR.'CP\PluginAdminController@updateCancel');

    });

});


