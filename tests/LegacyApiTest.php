<?php

class LegacyApiTest extends PrayerTestCase
{

    public function testSetParameters()
    {
        $provider = $this->getMockedProvider('2016', '6');
        $data = new TestPrayerData();

        $provider->shouldReceive('getTimesByCode')
            ->with('ext-308')
            ->once()
            ->andReturn($data);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/mpt.json?code=ext-308&year=2016&month=6&filter=3')
            ->assertResponseOk()
            ->seeJson([
                'meta' => [
                    'code' => 200
                ],
                'place' => 'Kajang'
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

        $this->get('/mpt.json?code=xxx-1&year=2016&month=6&filter=3')
            ->assertResponseStatus(400)
            ->seeJson([
                'code' => 400,
                'errorType' => 'ERROR_NO_PLACE',
                'error' => true
            ]);
    }
}
