<?php

namespace App\Providers;

use Geocoder\Provider\GoogleMaps;
use Geocoder\ProviderAggregator;
use Goutte\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Ivory\HttpAdapter\CurlHttpAdapter;
use League\Geotools\Geotools;
use Mpt\Provider;
use Mpt\Providers\Jakim\JakimProvider;

class PrayerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Provider::class, function (Application $app) {
            $adapter  = new CurlHttpAdapter();
            $geotools = new Geotools();
            $geocoder = new ProviderAggregator();
            $goutte = new Client();

            $geocoder->registerProviders([
                new GoogleMaps($adapter, null, null, true, env('MAPS_API_KEY')),
            ]);

            $jp = new JakimProvider($geotools, $geocoder, $goutte);
            
            $provider = new Provider();
            $provider->registerPrayerTimeProvider($jp);

            return $provider;
        });
    }
}
