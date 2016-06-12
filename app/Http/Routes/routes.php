<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web']], function () {
    //

    Route::get('/test', ['uses' => 'TestController@index']);
});

Route::group(['as' => 'api.', 'middleware' => ['api']], function () {

    // Legacy API

    Route::get('/mpt.json', ['as' => 'legacy', 'uses' => 'LegacyPrayerController@getApi']);

});
