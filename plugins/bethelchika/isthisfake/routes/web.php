<?php
Route::group(['middleware' => ['web']], function () {

    $CR="\\BethelChika\\IsThisFake\\Http\\Controllers\\";
    Route::get('/isthisfake/index', $CR.'HomeController@index')->name('isthisfake.index');
    Route::get('/isthisfake/settings', $CR.'HomeController@setting')->name('isthisfake.setting');
});