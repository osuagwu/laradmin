<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

$LCR="\\BethelChika\\Laradmin\\Http\\Controllers\\";//Laradmin Controller Roots

Route::group(['middleware' => ['web']], function () use ($LCR){
 

    // Userprofile routes
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
   
    Route::get('/u/alerts',$LCR.'User\UserProfileController@userAlerts')->name('user-alerts');

    Route::get('/u/avatar/',$LCR.'User\UserProfileController@avatar')->name('user-avatar');
    Route::get('/u/avatar/json',$LCR.'User\UserProfileController@avatarJson')->name('user-avatar-json');
    Route::post('/u/avatar/json',$LCR.'User\UserProfileController@avatarJsonStore');
    Route::delete('/u/avatar/json',$LCR.'User\UserProfileController@avatarJsonDelete');
    //Route::put('/u/avatar/json',$LCR.'User\UserProfileController@avatartJsonUpdate');

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
    Route::put('/u/auth-verification/xfactor',$LCR.'User\AuthVerificationController@xfactorUpdate')->name('user-auth-xfactor-update');

    /////////////////////////////////////////////////////////

    // AutoForm
    Route::get('/u/autoform/{pack}/{tag}/index',$LCR.'User\AutoformController@index')->name('user-autoform');
    Route::get('/u/autoform/{pack}/{tag}/edit',$LCR.'User\AutoformController@edit')->name('user-autoform-edit');
    Route::put('/u/autoform/{pack}/{tag}/edit',$LCR.'User\AutoformController@process');


    // Notifications
    Route::get('/u/notofication/index',$LCR.'User\NotificationController@index')->name('user-notification-index');
    Route::post('/u/notofication/mark-as-ajax',$LCR.'User\NotificationController@markAsAjax')->name('user-notification-mark-ajax');
    Route::delete('/u/notofication/{notofication}',$LCR.'User\NotificationController@destroy')->name('user-notification-delete');

    //User Message
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
    Route::put('/u/user-message/mark-as/ajax' ,$LCR.'User\UserMessageController@markAsAjax')->name('user-message-mark-ajax');
    Route::post('/u/user-message/reply',$LCR.'User\UserMessageController@reply')->name('user-message-reply');

    //Contact us
    Route::get('/u/contact-us/create' ,$LCR.'User\ContactUsUserMessageController@create')->name('contact-us-create');
    Route::post ('/u/contact-us',$LCR.'User\ContactUsUserMessageController@store')->name('contact-us-store');


    //SocialUser
    Route::get('/u/social-user', $LCR.'User\SocialUserController@index')->name('social-user');
    Route::get('/u/social-user/callout/{provider}/{callType?}', $LCR.'User\SocialUserController@redirectToProvider')->name('social-user-callout');
    Route::get('/u/social-user/callback/{provider}', $LCR.'User\SocialUserController@handleProviderCallback')->name('social-user-callback');
    Route::delete('/u/social-user/unlink/{socialUser}', $LCR.'User\SocialUserController@unlinkSocialUser')->name('social-user-delete');
    Route::get('/u/social-user-external', $LCR.'User\SocialUserController@externalAccounts')->name('social-user-external');

    Route::get('/u/social-user/link-email', $LCR.'User\SocialUserLinkEmailController@index')->name('social-user-link-email');
    Route::post('/u/social-user/link-email', $LCR.'User\SocialUserLinkEmailController@store')->name('social-user-link-email-create');
    Route::delete('/u/social-user/link-email/{socialUser}', $LCR.'User\SocialUserLinkEmailController@destroy')->name('social-user-link-email-delete');
    Route::get('/u/social-user/link-email/confirmation/{socialUser}/{token}', $LCR.'User\SocialUserLinkEmailController@linkEmailConfirmation')->name('social-user-link-email-confirm');
    Route::get('/u/social-user/link-email/resend-confirmation/{socialUser}', $LCR.'User\SocialUserLinkEmailController@resendConfirmationEmail')->name('social-user-link-email-confirm-resend');
    Route::put('/u/social-user/link-email/set-primary/{socialUser}', $LCR.'User\SocialUserLinkEmailController@setPrimaryEmail')->name('social-user-link-email-set-primary');

    // Feeds
    Route::get('/u/feed', $LCR.'User\FeedController@fetch')->name('user-feed');


    // Reauthentication
    Route::middleware(['auth'])->group(function ( ) use( $LCR) {
        Route::get('/u/re-auth/', $LCR.'User\UserProfileController@reAuthIndex')->name('re-auth');
        Route::post('/u/re-auth/', $LCR.'User\UserProfileController@reAuth');

        Route::get('/u/re-auth-social-user/{authSocialUser}', $LCR.'User\SocialUserController@reAuthWithSocialUser')->name('re-auth-social-user');

    });

 



    // WP (Wordpress)
    if(config('laradmin.wp_enable')){
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



    // //Admin entry point ****************************************************************
    // //****************************************************************************
    Route::prefix('cp')->group(function () use( $LCR){
        Route::get('/index', $LCR.'CP\ControlPanelController@index')->name('cp');
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

