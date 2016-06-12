<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PrayerRequest;
use League\Fractal\TransformerAbstract;
use Mpt\Model\PrayerData;
use Mpt\Provider;

class PrayerController extends ApiController
{

    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function code(PrayerRequest $request, $code)
    {
        $data = $this->provider
            ->setYear($request->getYear())
            ->setMonth($request->getMonth())
            ->getTimesByCode($code);

        return $this->response->item($data, new PrayerTransformer())
            ->setLastModified($data->getLastModified());
    }

    public function coordinate(PrayerRequest $request, $lat, $lng)
    {
        $data = $this->provider
            ->setYear($request->getYear())
            ->setMonth($request->getMonth())
            ->getTimesByCoordinate($lat, $lng);

        return $this->response->item($data, new PrayerTransformer())
            ->setLastModified($data->getLastModified());
    }
}

class PrayerTransformer extends TransformerAbstract
{
    public function transform(PrayerData $p)
    {
        return [
            'provider' => $p->getProviderName(),
            'code' => $p->getCode(),
            'year' => $p->getYear(),
            'month' => $p->getMonth(),
            'place' => $p->getPlace(),
            'times' => $p->getTimes(),
        ];
    }
}
