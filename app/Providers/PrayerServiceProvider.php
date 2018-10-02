<?php

namespace App\Providers;

use Geocoder\Provider\GoogleMaps;
use Geocoder\ProviderAggregator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Ivory\HttpAdapter\Guzzle6HttpAdapter;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\LaravelCacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Mpt\DatabaseCache;
use Mpt\Provider;
use Mpt\Providers\Jakim\YiiJakimProvider;
use Mpt\Providers\Muis\MuisProvider;

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
            $laravelCache = $app->make(Repository::class);
            $httpCache = new CacheMiddleware(new PrivateCacheStrategy(new LaravelCacheStorage($laravelCache)));

            $stack = HandlerStack::create();
            $stack->push($httpCache, 'cache');

            $guzzle = new GuzzleClient([
                'defaults' => [
                    'allow_redirects' => false,
                    'cookies' => true
                ],
                'handler' => $stack,
            ]);

            $geocoder = new ProviderAggregator();

            $cache = $app->make(DatabaseCache::class);
            $adapter = new Guzzle6HttpAdapter($guzzle);

            $geocoder->registerProviders([
                new GoogleMaps($adapter, null, null, true, config('app.maps_api_key')),
            ]);

            $httpClient = HttpClientDiscovery::find();
            $requestFactory = MessageFactoryDiscovery::find();

            $jp = new YiiJakimProvider($geocoder, $httpClient, $requestFactory);
            $mp = new MuisProvider($geocoder);

            $provider = new Provider();
            $provider->registerPrayerTimeProvider($jp);
            $provider->registerPrayerTimeProvider($mp);
            $provider->setCache($cache);

            return $provider;
        });
    }
}
