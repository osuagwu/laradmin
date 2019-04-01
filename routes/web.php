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

Route::get('/','HomeController@index')->name('home');

Route::get('/home', 'HomeController@index');
Route::get('/about', 'HomeController@about')->name('about');

Auth::routes();

// $LCR="\\App\\Laradmin\\Http\\Controllers\\";//Laradmin Controller Roots
// // Laradmins User Rgistration (THE output of Auth::routes() should be modified to remove the link to registration)
// Route::get('/u/register', $LCR.'User\RegisterController@showRegistrationForm')->name('user-register');
// Route::post('/u/register', $LCR.'User\RegisterController@register');

// /**
// * Laradmin routes
// **/
// // Userprofile routes
// Route::get('/u/index', $LCR.'User\UserProfileController@index')->name('user-home');
// Route::get('/u/profile', $LCR.'User\UserProfileController@profile')->name('user-profile');
// Route::get('/u/edit', $LCR.'User\UserProfileController@edit')->name('user-edit');
// Route::get('/u/security', $LCR.'User\UserProfileController@security')->name('user-security');
// Route::get('/u/edit-password', $LCR.'User\UserProfileController@editPassword')->name('user-edit-password');
// Route::put('/u/edit-password', $LCR.'User\UserProfileController@updatePassword');
// Route::put('/u/update', $LCR.'User\UserProfileController@update')->name('user-update');
// Route::get('/u/send-email-confirmation/',$LCR.'User\UserProfileController@sendEmailConfirmation')->name('send-email-confirmation');
// Route::get('/u/email-confirmation/{email}/{key}',$LCR.'User\UserProfileController@emailConfirmation')->name('email-confirmation');

// Route::get('/u/account-control',$LCR.'User\UserProfileController@accountControl')->name('user-account-control');
// Route::get('/u/delete',$LCR.'User\UserProfileController@initiateSelfDelete')->name('user-self-delete');
// Route::get('/u/cancel-delete',$LCR.'User\UserProfileController@cancelSelfDelete')->name('user-self-delete-cancel');
// Route::get('/u/deactivate',$LCR.'User\UserProfileController@selfDeactivate')->name('user-self-deactivate');
// Route::get('/u/reactivate',$LCR.'User\UserProfileController@selfReactivate')->name('user-self-reactivate');

// Route::get('/u/alerts',$LCR.'User\UserProfileController@userAlerts')->name('user-alerts');

// // Notifications
// Route::get('/u/notofication/index',$LCR.'User\NotificationController@index')->name('user-notification-index');
// Route::post('/u/notofication/mark-as-ajax',$LCR.'User\NotificationController@markAsAjax')->name('user-notification-mark-ajax');
// Route::delete('/u/notofication/{notofication}',$LCR.'User\NotificationController@destroy')->name('user-notification-delete');

// //User Message
// Route::delete('/user-message/' ,$LCR.'User\UserMessageController@destroys')->name('user-message-deletes');
// //Resource-------------------------------
// Route::post ('/user-message',$LCR.'User\UserMessageController@store')->name('user-message-store');
// Route::get('/user-message' ,$LCR.'User\UserMessageController@index')->name('user-message-index');
// Route::get('/user-message/create' ,$LCR.'User\UserMessageController@create')->name('user-message-create');
// Route::delete('/user-message/{message}' ,$LCR.'User\UserMessageController@destroy')->name('user-message-delete');
// Route::get('/user-message/{message}',$LCR.'User\UserMessageController@show')->name('user-message-show');
// Route::put('/user-message/{message}' ,$LCR.'User\UserMessageController@update')->name('user-message-update');
// Route::get('/user-message/{message}/edit ',$LCR.'User\UserMessageController@edit')->name('user-message-edit');
// //-----------------------------------------------
// //Route::resource('/cp/message',$LCR.'CP\UserMessageController');
// Route::put('/user-message/mark-as/ajax' ,$LCR.'User\UserMessageController@markAsAjax')->name('user-message-mark-ajax');
// Route::post('/user-message/reply',$LCR.'User\UserMessageController@reply')->name('user-message-reply');

// //Contact us
// Route::get('/contact-us/create' ,$LCR.'User\ContactUsUserMessageController@create')->name('contact-us-create');
// Route::post ('/contact-us',$LCR.'User\ContactUsUserMessageController@store')->name('contact-us-store');


// //SocialUser
// Route::get('/u/social-user', $LCR.'User\SocialUserController@index')->name('social-user');
// Route::get('/u/social-user/callout/{provider}/{callType?}', $LCR.'User\SocialUserController@redirectToProvider')->name('social-user-callout');
// Route::get('/u/social-user/callback/{provider}', $LCR.'User\SocialUserController@handleProviderCallback')->name('social-user-callback');
// Route::delete('/u/social-user/unlink/{socialUser}', $LCR.'User\SocialUserController@unlinkSocialUser')->name('social-user-delete');

// Route::get('/u/social-user-external', $LCR.'User\SocialUserController@externalAccounts')->name('social-user-external');
// Route::get('/u/social-user/link-email', $LCR.'User\SocialUserLinkEmailController@index')->name('social-user-link-email');
// Route::post('/u/social-user/link-email', $LCR.'User\SocialUserLinkEmailController@store')->name('social-user-link-email-create');
// Route::delete('/u/social-user/link-email/{socialUser}', $LCR.'User\SocialUserLinkEmailController@destroy')->name('social-user-link-email-delete');
// Route::get('/u/social-user/link-email/confirmation/{socialUser}/{key}', $LCR.'User\SocialUserLinkEmailController@linkEmailConfirmation')->name('social-user-link-email-confirm');
// Route::get('/u/social-user/link-email/resend-confirmation/{socialUser}', $LCR.'User\SocialUserLinkEmailController@resendConfirmationEmail')->name('social-user-link-email-confirm-resend');
// Route::put('/u/social-user/link-email/set-primary/{socialUser}', $LCR.'User\SocialUserLinkEmailController@setPrimaryEmail')->name('social-user-link-email-set-primary');

// // Reauthentication
// Route::middleware(['auth'])->group(function ( ) use( $LCR) {
//     //$this->middleware('auth');
//     Route::get('/u/re-auth/', $LCR.'User\UserProfileController@reAuthIndex')->name('re-auth');
//     Route::post('/u/re-auth/', $LCR.'User\UserProfileController@reAuth');
//     Route::get('/u/re-auth-social-user/{authSocialUser}', $LCR.'User\SocialUserController@reAuthWithSocialUser')->name('re-auth-social-user');

// });


// //Admin pages ****************************************************************
// //****************************************************************************
// Route::get('/cp/index', $LCR.'CP\ControlPanelController@index')->name('cp');

// // Users
// Route::get('/cp/users', $LCR.'CP\UserController@index')->name('cp-users');
// Route::get('/cp/user/{user}/show/', $LCR.'CP\UserController@show')->name('cp-user');
// Route::get('/cp/user/{user}/edit/', $LCR.'CP\UserController@edit')->name('cp-user-edit');
// Route::put('/cp/user/{user}/update/', $LCR.'CP\UserController@update')->name('cp-user-update');

// Route::get('/cp/user/create/',$LCR.'CP\UserController@create')->name('cp-user-create');
// Route::post('/cp/user/create/',$LCR.'CP\UserController@store')->name('cp-user-store');

// Route::delete('/cp/user/{user}/',$LCR.'CP\UserController@destroy')->name('cp-user-delete');
// Route::delete('/cp/users/',$LCR.'CP\UserController@destroys')->name('cp-users-delete');

// Route::get('/cp/send-email-confirmation/{user}',$LCR.'CP\UserController@sendEmailConfirmation')->name('cp-send-email-confirmation');
// Route::get('/cp/email-confirmation/{user}/',$LCR.'CP\UserController@emailConfirmation')->name('cp-email-confirmation');

// Route::get('/cp/user/{user}/disable',$LCR.'CP\UserController@disableUser')->name('cp-user-disable');
// Route::get('/cp/user/{user}/enable',$LCR.'CP\UserController@enableUser')->name('cp-user-enable');

// //User group map
// Route::get('/cp/user/{user}/group-map/edits/',$LCR.'CP\UserGroupMapController@edits')->name('cp-user-group-map-edits');
// Route::put('/cp/user/{user}/group-map/updates/',$LCR.'CP\UserGroupMapController@updates')->name('cp-user-group-map-updates');

// // User groups
// Route::get('/cp/user-groups/',$LCR.'CP\UserGroupController@index')->name('cp-user-groups');
// Route::get('/cp/user-group/{userGroup}/show',$LCR.'CP\UserGroupController@show')->name('cp-user-group');
// Route::get('/cp/user-group/{userGroup}/edit',$LCR.'CP\UserGroupController@edit')->name('cp-user-group-edit');
// Route::put('/cp/user-group/{userGroup}/',$LCR.'CP\UserGroupController@update')->name('cp-user-group-update');
// Route::get('/cp/user-group/create/',$LCR.'CP\UserGroupController@create')->name('cp-user-group-create');
// Route::post('/cp/user-group/create/',$LCR.'CP\UserGroupController@store')->name('cp-user-group-store');
// Route::delete('/cp/user-group/{userGroup}/',$LCR.'CP\UserGroupController@destroy')->name('cp-user-group-delete');
// Route::delete('/cp/user-groups/',$LCR.'CP\UserGroupController@destroys')->name('cp-user-groups-delete');

// // Sources
// Route::get('/cp/sources/',$LCR.'CP\SourceController@index')->name('cp-sources');
// Route::get('/cp/source/table/{table}',$LCR.'CP\SourceController@showTable')->name('cp-source-table');
// Route::put('/cp/source/permissions',$LCR.'CP\SourceController@updatePermission')->name('cp-source-permission-update');
// Route::get('/cp/source/permissions/search-users',$LCR.'CP\SourceController@searchUsers')->name('cp-source-permission-search-users');
// Route::post('/cp/source/permissions/store-permission',$LCR.'CP\SourceController@storePermission')->name('cp-source-permission-store');
// Route::delete('/cp/source/permissions/delete-permission',$LCR.'CP\SourceController@destroyPermission')->name('cp-source-permission-delete');

// //CP User Message____________
// Route::delete('cp/user-message/' ,$LCR.'CP\UserMessageController@destroys')->name('cp-user-message-deletes');
// //Resource-------------------------------
// Route::post ('cp/user-message',$LCR.'CP\UserMessageController@store')->name('cp-user-message-store');
// Route::get('cp/user-message' ,$LCR.'CP\UserMessageController@index')->name('cp-user-message-index');
// Route::get('cp/user-message/create' ,$LCR.'CP\UserMessageController@create')->name('cp-user-message-create');
// Route::delete('cp/user-message/{message}' ,$LCR.'CP\UserMessageController@destroy')->name('cp-user-message-delete');
// Route::get('cp/user-message/{message}',$LCR.'CP\UserMessageController@show')->name('cp-user-message-show');
// Route::put('cp/user-message/{message}' ,$LCR.'CP\UserMessageController@update')->name('cp-user-message-update');
// Route::get('cp/user-message/{message}/edit ',$LCR.'CP\UserMessageController@edit')->name('cp-user-message-edit');
// //-----------------------------------------------
// //Route::resource('/cp/message',$LCR.'CP\UserMessageController');
// Route::put('cp/user-message/mark-as/ajax' ,$LCR.'CP\UserMessageController@markAsAjax')->name('cp-user-message-mark-ajax');
// Route::post('cp/user-message/reply',$LCR.'CP\UserMessageController@reply')->name('cp-user-message-reply');

// // Notifications
// Route::get('/cp/notification/index',$LCR.'CP\NotificationController@index')->name('cp-notification-index');
// Route::post('/cp/notification/mark-as-ajax',$LCR.'CP\NotificationController@markAsAjax')->name('cp-notification-mark-ajax');
// Route::delete('/cp/notification/{notification}',$LCR.'CP\NotificationController@destroy')->name('cp-notification-delete');

// // General seetings
// Route::get('/cp/settings/edit',$LCR.'CP\SettingsController@edit')->name('cp-settings-edit');



