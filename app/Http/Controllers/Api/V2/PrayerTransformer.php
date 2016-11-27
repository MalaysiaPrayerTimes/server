<?php

namespace App\Http\Controllers\Api\V2;

use League\Fractal\TransformerAbstract;
use Mpt\Model\PrayerData;

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
            'attributes' => $p->getExtraAttributes(),
            'times' => $p->getTimes(),
        ];
    }
}
