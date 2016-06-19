<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web']], function () {
    //

    Route::get('/test', ['uses' => 'TestController@index']);
});

