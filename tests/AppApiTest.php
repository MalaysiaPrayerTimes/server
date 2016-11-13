<?php

class AppApiTest extends TestCase
{
    public function testGetSupportedCodes()
    {
        $data = [
            'test' => [
                new TestPrayerCode(),
            ],
        ];

        $provider = Mockery::mock(\Mpt\Provider::class);

        $provider->shouldReceive('getSupportedCodes')
            ->once()
            ->andReturn($data);

        $this->app->instance(\Mpt\Provider::class, $provider);

        $this->get('/api/app/codes')
            ->assertResponseOk()
            ->seeJsonStructure([
                '*' => [
                    'provider',
                    'codes',
                ],
            ])
            ->seeJson([
                'provider' => 'test',
            ])
            ->seeJson([
                'code' => 'code',
                'city' => 'city',
                'state' => 'state',
                'country' => 'MY',
            ]);
    }
}

class TestPrayerCode extends \Mpt\Model\AbstractPrayerCode
{

    /**
     * @return string
     */
    public function getCode()
    {
        return 'code';
    }

    /**
     * return string
     */
    public function getCity()
    {
        return 'city';
    }

    /**
     * return string
     */
    public function getState()
    {
        return 'state';
    }

    /**
     * return string
     */
    public function getCountry()
    {
        return 'MY';
    }

    /**
     * return string
     */
    public function getProviderName()
    {
        return 'test';
    }
}
