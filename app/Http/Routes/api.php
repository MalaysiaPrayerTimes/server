<?php

use Dingo\Api\Routing\Router;

/**
 * @var $api \Dingo\Api\Routing\Router
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v2', ['middleware' => 'api.throttle', 'limit' => 30, 'expires' => 1, 'namespace' => 'App\Http\Controllers\Api\V2'], function (Router $api) {

    $api->get('prayer/{code}', 'PrayerController@code')
        ->where('code', '[a-z]{3}-[0-9]+');
    
    $api->get('prayer/{lat},{lng}', 'PrayerController@coordinate')
        ->where('lat', '^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$')
        ->where('lng', '^[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$');
});