<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| TODO: COnsider removing the functionality of looking for controllers in the main apps folder. 
        Programmers can achieve the intended effect simply by overriding a related route and 
        providing a custom controller. 
|
*/
// Route::group(['middleware' => ['web']], function () {
//     Route::get('/up0/index', function () {
//         dd('ff');
//     })->name('fff');
// });

Route::group(['middleware' => ['web']], function () {

    $LCR_LOCAL="\\BethelChika\\Laradmin\\Http\\Controllers\\";//Laradmin Controller Roots
    $LCR_EXT="\\App\\Http\\Controllers\\Laradmin\\";// User own implementation of Laradmin controller roots


    // Laradmins User Rgistration 
    //$LCR=class_exists($LCR_EXT.'User\RegisterController')?$LCR_EXT:$LCR_LOCAL;
    //????Route::get('/u/register', $LCR.'User\RegisterController@showRegistrationForm')->name('user-register');
    //????Route::post('/u/register', $LCR.'User\RegisterController@register');



    // Userprofile routes
    $LCR=class_exists($LCR_EXT.'User\UserProfileController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/index', $LCR.'User\UserProfileController@index')->name('user-home');
    Route::get('/u/profile/{form_pack?}/{form_tag?}', $LCR.'User\UserProfileController@profile')->name('user-profile');
    Route::get('/u/edit_profile/{form_pack}/{form_tag}', $LCR.'User\UserProfileController@edit')->name('user-profile-edit');
    Route::put('/u/edit_profile/{form_pack}/{form_tag}', $LCR.'User\UserProfileController@update');
    Route::get('/u/settings', $LCR.'User\UserProfileController@settings')->name('user-settings');
    
    Route::get('/u/security', $LCR.'User\UserProfileController@security')->name('user-security');
    Route::get('/u/security/security-questions',$LCR.'User\UserProfileController@securityQuestions')->name('user-security-questions');
    Route::get('/u/security/security-questions/edit',$LCR.'User\UserProfileController@securityQuestionsEdit')->name('user-security-questions-edit');
    Route::match(['post','put'],'/u/security/security-questions/edit',$LCR.'User\UserProfileController@securityQuestionsUpdate');

    Route::get('/u/security/login-attempts',$LCR.'User\UserProfileController@loginAttempts')->name('user-login-attempts');
    Route::delete('/u/security/login-attempt/{attempt_id}',$LCR.'User\UserProfileController@loginAttemptDestroy')->name('user-login-attempt');

  


    


    Route::get('/u/edit-password', $LCR.'User\UserProfileController@editPassword')->name('user-edit-password');
    Route::put('/u/edit-password', $LCR.'User\UserProfileController@updatePassword');

    
    Route::get('/u/email-confirmation',$LCR.'User\UserProfileController@sendEmailConfirmation')->name('send-email-confirmation');
    Route::get('/u/email-confirmation/confirm/{email}/{token}',$LCR.'User\UserProfileController@emailConfirmation')->name('email-confirmation');

    Route::get('/u/account-control',$LCR.'User\UserProfileController@accountControl')->name('user-account-control');
    Route::get('/u/delete',$LCR.'User\UserProfileController@initiateSelfDelete')->name('user-self-delete');
    Route::get('/u/cancel-delete',$LCR.'User\UserProfileController@cancelSelfDelete')->name('user-self-delete-cancel');
    Route::get('/u/deactivate',$LCR.'User\UserProfileController@selfDeactivate')->name('user-self-deactivate');
    Route::get('/u/reactivate',$LCR.'User\UserProfileController@selfReactivate')->name('user-self-reactivate');
    //Route::get('/u/plugin-settings',$LCR.'User\UserProfileController@pluginSettings')->name('user-plugin-settings');
    Route::get('/u/alerts',$LCR.'User\UserProfileController@userAlerts')->name('user-alerts');

    
    // Privacy
    Route::get('/u/privacy/',$LCR.'User\UserProfileController@privacy')->name('user-privacy');

    // Billing 
    Route::get('/u/billing',$LCR.'User\BillingController@index')->name('user-billing');
    Route::get('/u/billing/payment-methods',$LCR.'User\BillingController@paymentMethods')->name('user-billing-methods');
    Route::get('/u/billing/payment-methods/create',$LCR.'User\BillingController@paymentMethodCreate')->name('user-billing-method-create');
    Route::match(['post','put'],'/u/billing/payment-methods/create',$LCR.'User\BillingController@paymentMethodStore');
    Route::delete('/u/billing/payment-methods/create',$LCR.'User\BillingController@paymentMethodDestroy');

    // Billing - single payment
    Route::get('/u/billing/payment/arbitrary',$LCR.'User\BillingController@arbitraryPayment')->name('user-billing-pay-arb');
    Route::get('/u/billing/payment/arbitrary/create',$LCR.'User\BillingController@arbitraryPaymentCreate')->name('user-billing-pay-arb-c');
    Route::post('/u/billing/payment/arbitrary/create',$LCR.'User\BillingController@arbitraryPaymentStore');

    //Billing - subscriptions
    Route::get('/u/billing/subscription',$LCR.'User\BillingController@subscriptionIndex')->name('user-billing-subs');
    Route::get('/u/billing/subscription/subscribe/step1',$LCR.'User\BillingController@subscriptionCreateStep1')->name('user-billing-sub1');
    Route::post('/u/billing/subscription/subscribe/step1',$LCR.'User\BillingController@subscriptionHandleStep1');
    Route::get('/u/billing/subscription/subscribe/step2',$LCR.'User\BillingController@subscriptionCreateStep2')->name('user-billing-sub2');
    Route::post('/u/billing/subscription/subscribe/step2',$LCR.'User\BillingController@subscriptionHandleStep2');
    
    Route::get('/u/billing/subscription/{name}/swap',$LCR.'User\BillingController@swapCreate')->name('user-billing-sub-swap');
    Route::put('/u/billing/subscription/{name}/swap',$LCR.'User\BillingController@swap');

    Route::get('/u/billing/subscription/{name}/quantity',$LCR.'User\BillingController@quantityCreate')->name('user-billing-sub-quantity');
    Route::put('/u/billing/subscription/{name}/quantity',$LCR.'User\BillingController@quantity');

    Route::put('/u/billing/subscription/{name}/update-action/{update_action}',$LCR.'User\BillingController@subscriptionUpdateAction')->name('user-billing-sub-action');

    // Billing - subscription plans
    Route::get('/u/billing/subscription/plans',$LCR.'User\BillingController@subscriptionPlans')->name('user-billing-sub-plans');

    // Billing invoice
    Route::get('/u/billing/invoice',$LCR.'User\BillingController@invoiceIndex')->name('user-billing-invoices');
    Route::get('/u/billing/invoice/{invoice_id}',$LCR.'User\BillingController@invoice')->name('user-billing-invoice');

    // Auth verification routes
    $LCR=class_exists($LCR_EXT.'User\AuthVerificationController')?$LCR_EXT:$LCR_LOCAL;
    Route::get('/u/auth-verification',$LCR.'User\AuthVerificationController@index')->name('user-auth-v');
    Route::put('/u/auth-verification',$LCR.'User\AuthVerificationController@process');
    Route::get('/u/auth-verification/done',$LCR.'User\AuthVerificationController@done')->name('user-auth-v-done');
    
    // Auth verification channels routes
    Route::get('/u/auth-verification/channel/email',$LCR.'User\AuthVerificationController@email');
    Route::post('/u/auth-verification/channel/email',$LCR.'User\AuthVerificationController@emailSendCode');
    Route::get('/u/auth-verification/channel/email/code/{email_id}',$LCR.'User\AuthVerificationController@emailCode')->name('user-auth-v-email-step3');
    Route::put('/u/auth-verification/channel/email',$LCR.'User\AuthVerificationController@emailVerify');
    
    Route::get('/u/auth-verification/channel/security_question',$LCR.'User\AuthVerificationController@securityQuestion');
    Route::put('/u/auth-verification/channel/security_question',$LCR.'User\AuthVerificationController@securityQuestionVerify');
    
    Route::get('/u/auth-verification/channel/password',$LCR.'User\AuthVerificationController@password');
    Route::put('/u/auth-verification/channel/password',$LCR.'User\AuthVerificationController@passwordVerify');
    

    // Auth verification implementation extra factor authentication
    //Route::get('/u/auth-verification/xfactor',$LCR.'User\AuthVerificationController@xfactor');
    Route::put('/u/auth-verification/xfactor',$LCR.'User\AuthVerificationController@xfactorUpdate')->name('user-auth-xfactor-update');

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
    Route::delete('/u/user-message/' ,$LCR.'User\UserMessageController@destroys')->name('user-message-deletes');
    //Resource-------------------------------
    Route::post ('/u/user-message',$LCR.'User\UserMessageController@store')->name('user-message-store');
    Route::get('/u/user-message' ,$LCR.'User\UserMessageController@index')->name('user-message-index');
    Route::get('/u/user-message/create' ,$LCR.'User\UserMessageController@create')->name('user-message-create');
    Route::delete('/u/user-message/{message}' ,$LCR.'User\UserMessageController@destroy')->name('user-message-delete');
    Route::get('/u/user-message/{message}',$LCR.'User\UserMessageController@show')->name('user-message-show');
    Route::put('/u/user-message/{message}' ,$LCR.'User\UserMessageController@update')->name('user-message-update');
    Route::get('/u/user-message/{message}/edit ',$LCR.'User\UserMessageController@edit')->name('user-message-edit');
    //-----------------------------------------------
    //Route::resource('/cp/message',$LCR.'CP\UserMessageController');
    Route::put('/u/user-message/mark-as/ajax' ,$LCR.'User\UserMessageController@markAsAjax')->name('user-message-mark-ajax');
    Route::post('/u/user-message/reply',$LCR.'User\UserMessageController@reply')->name('user-message-reply');

    //Contact us
    Route::get('/u/contact-us/create' ,$LCR.'User\ContactUsUserMessageController@create')->name('contact-us-create');
    Route::post ('/u/contact-us',$LCR.'User\ContactUsUserMessageController@store')->name('contact-us-store');


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
    Route::get('/u/social-user/link-email/confirmation/{socialUser}/{token}', $LCR.'User\SocialUserLinkEmailController@linkEmailConfirmation')->name('social-user-link-email-confirm');
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
    if(config('laradmin.wp_enable')){
        $LCR=class_exists($LCR_EXT.'User\WPController')?$LCR_EXT:$LCR_LOCAL;
        // Page
        Route::prefix(config('laradmin.page_url_prefix'))->group(function () use( $LCR){
            Route::get('home', $LCR.'User\WPController@homePage')->name('laradmin-homepage');
            Route::get('{slug}', $LCR.'User\WPController@page')->name('page');
            
        });

        Route::get('/_page-wp-to-laradmin/{slug}',function($slug){//NOTE: REF:PAGE-ROUTE-URL-PRE-WP-PLUG-1 If you make changes to this route url, you should also update laradmin wp plugin.
            //For a page redirected from Wordpress by the laradmin plugin, Wordpress custom links etc.
            return redirect()->route('page',$slug);
        });

        // Laradmin Larus posts
        Route::prefix(config('laradmin.larus_post_url_prefix','larus-post'))->group(function () use( $LCR){
            Route::get('{slug}', $LCR.'User\WPController@larusPost')->name('larus-post');
        });
        Route::get('/_larus-post-wp-to-laradmin/{slug}',function($slug){//NOTE: REF:LARUS-POST-ROUTE-URL-PRE-WP-PLUG-1 If you make changes to this route url, you should also update laradmin wp plugin.
            //For a post redirected from Wordpress by the laradmin plugin, Wordpress custom links etc.
            return redirect()->route('larus-post',$slug);
        });
    
        // General post
        Route::prefix('post')->group(function () use( $LCR){
            Route::get('{slug}', $LCR.'User\WPController@post')->name('post');
        });

        Route::get('/_post-wp-to-laradmin/{slug}',function($slug){//NOTE: REF:POST-ROUTE-URL-PRE-WP-PLUG-1 If you make changes to this route url, you should also update laradmin wp plugin.
            //For a post redirected from Wordpress by the laradmin plugin, Wordpress custom links etc.
            return redirect()->route('post',$slug);
        });
    
       
        // In case of faulty wordpress
        Route::get(config('laradmin.wp_rpath').'/{slug}',function(){
            //This should never execute as it is installed in config('laradmin.wp_rpath'), Wordpress should already grab the request right?
            return 'Problem with WordPress';
        })->name('wp');

        // Comments
        Route::get('/post-comments', $LCR.'User\WPController@fetchComments')->name('post-comments');
        Route::post('/post-comments', $LCR.'User\WPController@createComment');
    }






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

        Route::get('/email-confirmation/send/{user}',$LCR.'CP\UserController@sendEmailConfirmation')->name('cp-send-email-confirmation');
        Route::get('/email-confirmation/confirm/{user}/',$LCR.'CP\UserController@emailConfirmation')->name('cp-email-confirmation');

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
        Route::get('/source/types/create',$LCR.'CP\SourceController@create')->name('cp-source-create');
        Route::post('/source/create',$LCR.'CP\SourceController@store');
        //Route::get('/sources/',$LCR.'CP\SourceController@index')->name('cp-sources');
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

        // Settings
        $LCR=class_exists($LCR_EXT.'CP\SettingsController')?$LCR_EXT:$LCR_LOCAL;
        Route::get('/settings/{form_pack?}/{form_tag?}',$LCR.'CP\SettingsController@index')->name('cp-settings');

        //Route::get('/settings_edit',$LCR.'CP\SettingsController@edit')->name('cp-settings-edit');


        // Post Installation setings
        Route::get('/post_install',$LCR.'CP\SettingsController@postInstall')->name('cp-post-install');
        Route::put('/post_install/wpitems',$LCR.'CP\SettingsController@wpInstallItems')->name('cp-post-install-wpitems');
        Route::get('/post_install/storage-link',$LCR.'CP\SettingsController@storageLink')->name('cp-post-install-storage-link');

        // Plugins
        $LCR=class_exists($LCR_EXT.'CP\PluginAdminController')?$LCR_EXT:$LCR_LOCAL;
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

/**
 * STRIPE WEBHOOK
 * 
 * Since Laradmin is a package it is not reliable to override the Cashier's webhook route 
 * as instructed since it will depend on which(Laradmin's or Cashier's routes) loads first.
 * For this reason, we should create our own webhook route but extends Cashiers controller 
 * in our own controller.
 */
Route::prefix(config('cashier.path'))->group( function () {
    Route::post('extra-webhook','\BethelChika\Laradmin\Billing\Stripe\Http\Controllers\WebhookController@handleWebhook');
});

