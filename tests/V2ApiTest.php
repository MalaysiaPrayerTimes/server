<?php

class V2ApiTest extends PrayerTestCase
{
    public function testSetParameters()
    {
        $provider = $this->getMockedProvider('2016', '6');
        $data = new TestPrayerData();

        $provider->shouldReceive('getTimesByCode')
            ->with('ext-308')
            ->once()
            ->andReturn($data);

        $provider->shouldReceive('getTimesByCoordinates')
            ->with('3.28011', '101.556')
            ->once()
            ->andReturn($data);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/ext-308?year=2016&month=6')
            ->assertResponseOk()
            ->seeJson([
                'code' => 'ext-308',
                'place' => 'Kajang',
                'month' => 6,
                'year' => 2016
            ]);

        $this->get('/api/prayer/3.28011,101.556?year=2016&month=6')
            ->assertResponseOk()
            ->seeJson([
                'code' => 'ext-308',
                'place' => 'Kajang',
                'month' => 6,
                'year' => 2016
            ]);
    }

    public function testInvalidCode()
    {
        $provider = $this->getMockedProvider('2016', '6');

        $provider->shouldReceive('getTimesByCode')
            ->with('xxx-1')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\InvalidCodeException()]);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/xxx-1?year=2016&month=6')
            ->assertResponseStatus(404)
            ->seeJson([
                'status_code' => 404
            ]);
    }

    public function testUnsupportedCoordinates()
    {
        $provider = $this->getMockedProvider('', '');

        $provider->shouldReceive('getTimesByCoordinates')
            ->with('1.3147268', '103.8116508')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\DataNotAvailableException()]);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/1.3147268,103.8116508')
            ->assertResponseStatus(404)
            ->seeJson([
                'status_code' => 404
            ]);
    }

    public function testEmptyCode()
    {
        $provider = $this->getMockedProvider();

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer')
            ->assertResponseStatus(404)
            ->seeJson([
                'status_code' => 404
            ]);
    }

    public function testInvalidData()
    {
        $provider = $this->getMockedProvider('', '');

        $provider->shouldReceive('getTimesByCode')
            ->with('wlp-0')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\InvalidDataException()]);

        $provider->shouldReceive('getTimesByCoordinates')
            ->with('3.28011', '101.556')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\InvalidDataException()]);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/wlp-0')
            ->assertResponseStatus(502)
            ->seeJson([
                'status_code' => 502
            ]);

        $this->get('/api/prayer/3.28011,101.556')
            ->assertResponseStatus(502)
            ->seeJson([
                'status_code' => 502
            ]);
    }

    public function testProviderCannotConnect()
    {
        $provider = $this->getMockedProvider('', '');

        $provider->shouldReceive('getTimesByCode')
            ->with('wlp-0')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\ConnectException()]);

        $provider->shouldReceive('getTimesByCoordinates')
            ->with('3.28011', '101.556')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\ConnectException()]);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/wlp-0')
            ->assertResponseStatus(504)
            ->seeJson([
                'status_code' => 504
            ]);

        $this->get('/api/prayer/3.28011,101.556')
            ->assertResponseStatus(504)
            ->seeJson([
                'status_code' => 504
            ]);
    }

    public function testKnownProviderException()
    {
        $provider = $this->getMockedProvider('', '');

        $provider->shouldReceive('getTimesByCode')
            ->with('wlp-0')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\ProviderException('Known provider exception.')]);

        $provider->shouldReceive('getTimesByCoordinates')
            ->with('3.28011', '101.556')
            ->once()
            ->andThrowExceptions([new \Mpt\Exception\ProviderException('Known provider exception.')]);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/wlp-0')
            ->assertResponseStatus(500)
            ->seeJson([
                'status_code' => 500
            ]);

        $this->get('/api/prayer/3.28011,101.556')
            ->assertResponseStatus(500)
            ->seeJson([
                'status_code' => 500
            ]);
    }

    public function testRandomProviderException()
    {
        $provider = $this->getMockedProvider('', '');

        $provider->shouldReceive('getTimesByCode')
            ->with('wlp-0')
            ->once()
            ->andThrowExceptions([new \Exception('Random provider exception.')]);

        $provider->shouldReceive('getTimesByCoordinates')
            ->with('3.28011', '101.556')
            ->once()
            ->andThrowExceptions([new \Exception('Random provider exception.')]);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/prayer/wlp-0')
            ->assertResponseStatus(500)
            ->seeJson([
                'status_code' => 500
            ]);

        $this->get('/api/prayer/3.28011,101.556')
            ->assertResponseStatus(500)
            ->seeJson([
                'status_code' => 500
            ]);
    }
}
