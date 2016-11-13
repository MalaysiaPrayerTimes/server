<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ApiController;
use Mpt\Model\PrayerCode;
use Mpt\Provider;
use Request;

class AppController extends ApiController
{
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function codes(Request $request)
    {
        $data = $this->provider->getSupportedCodes();
        $codes = collect($data);

        $codes->transform(function ($item, $key) {
            return [
                'provider' => $key,
                'codes' => collect($item)
                    ->transform(function (PrayerCode $item, $key) {
                        return [
                            'code' => $item->getCode(),
                            'city' => $item->getCity(),
                            'state' => $item->getState(),
                            'country' => $item->getCountry(),
                        ];
                    })
            ];
        });

        $response = response()
            ->json($codes->values()->all());

        $response->setEtag(md5($response->getContent()), true);

        return $response;
    }
}
