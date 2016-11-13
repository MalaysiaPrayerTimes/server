<?php

use Dingo\Api\Routing\Router;

Route::group(['as' => 'api.', 'middleware' => ['api'], 'namespace' => 'Api'], function () {

    // Legacy API

    Route::get('/mpt.json', ['as' => 'legacy', 'uses' => 'LegacyPrayerController@getApi']);
});

/**
 * @var $api \Dingo\Api\Routing\Router
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v2', ['middleware' => ['api'], 'namespace' => 'App\Http\Controllers\Api\V2'], function (Router $api) {
    $api->group(['prefix' => 'prayer'], function (Router $api) {
        $api->get('{code}', 'PrayerController@code')
            ->where('code', '[a-z]{3}-[0-9]+');

        $api->get('{lat},{lng}', 'PrayerController@coordinate')
            ->where('lat', '^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$')
            ->where('lng', '^[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$');
    });

    $api->group(['prefix' => 'app'], function (Router $api) {
        $api->get('codes', 'AppController@codes');
    });
});
