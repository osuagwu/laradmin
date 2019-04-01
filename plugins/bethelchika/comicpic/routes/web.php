<?php
Route::group(['middleware' => ['web']], function () {

    $CR="\\BethelChika\\Comicpic\\Http\\Controllers\\";
    Route::get('/comicpic/index', $CR.'HomeController@index')->name('comicpic.index');
    Route::get('/comicpic/show/{comicpic}', $CR.'HomeController@show')->name('comicpic.show');
    Route::get('/comicpic/og/{comicpic}', $CR.'HomeController@og')->name('comicpic.og');

    Route::get('/comicpic/settings', $CR.'UserController@setting')->name('comicpic.setting');


    Route::get('/comicpic/create', $CR.'UserController@create')->name('comicpic.create');
    Route::post('/comicpic/create', $CR.'UserController@store');
    Route::get('/comicpic/edit/{comicpic_id}', $CR.'UserController@edit')->name('comicpic.edit');
    Route::put('/comicpic/update', $CR.'UserController@update')->name('comicpic.update');
    Route::delete('/comicpic/delete/{comicpic}', $CR.'UserController@destroy')->name('comicpic.delete');
    Route::put('/comicpic/publish/{comicpic}', $CR.'UserController@publish')->name('comicpic.publish');
    Route::put('/comicpic/unpublish/{comicpic}', $CR.'UserController@unpublish')->name('comicpic.unpublish');

    Route::get('/comicpic/me', $CR.'UserController@me')->name('comicpic.me');

    //\admin setitngs
    Route::get('/comicpic/admin', $CR.'AdminController@index')->name('comicpic.admin');
    Route::get('/comicpic/admin/create', $CR.'AdminController@create')->name('comicpic.admin-create');
    Route::post('/comicpic/admin/create', $CR.'AdminController@store')->name('comicpic.admin-create');
    Route::get('/comicpic/admin/show/{comicpic}', $CR.'AdminController@show')->name('comicpic.admin-show');
    Route::delete('/comicpic/admin/deletes', $CR.'AdminController@destroys')->name('comicpic.admin-deletes');
    Route::delete('/comicpic/admin/delete/{comicpic}', $CR.'AdminController@destroy')->name('comicpic.admin-delete');

    Route::get('/comicpic/admin/settings', $CR.'AdminController@editSettings')->name('comicpic.admin-edit-settings');
    Route::put('/comicpic/admin/settings', $CR.'AdminController@updateSettings');

    // User settings
    Route::get('/comicpic/user_settings', $CR.'UserController@settings')->name('comicpic.user_settings');
    
});