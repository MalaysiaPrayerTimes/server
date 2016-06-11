<?php

namespace Mpt;

use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;
use Mpt\Model\LocationCache;
use Mpt\Model\PrayerData;

class DatabaseCache implements CacheInterface
{

    private $cache;
    private $db;

    public function __construct(Repository $cache, Connection $db)
    {
        $this->cache = $cache;
        $this->db = $db;
    }

    /**
     * @param $code
     * @return PrayerData|null
     */
    public function getPrayerData($code, $year, $month)
    {
        return $this->cache->get($this->getCacheId($code, $year, $month));
    }

    public function cachePrayerData(PrayerData $data)
    {
        $this->cache->forever($this->getCacheId($data->getCode(), $data->getYear(), $data->getMonth()), $data);
    }

    /**
     * Probably better to use redis 3.2.0 for this
     *
     * @param $lat
     * @param $lng
     * @param int $radius
     * @return LocationCache|null
     */
    public function getCodeByLocation($lat, $lng, $radius = 5)
    {
        $distance = "6371 * acos(" .
            "cos(radians($lat)) * " .
            "cos(radians(lat)) * " .
            "cos(radians(lng) - radians($lng)) + " .
            "sin(radians($lat)) * " .
            "sin(radians(lat))" .
            ") AS distance";

        $r = $this->db->table('location_cache')
            ->select()
            ->selectRaw($distance)
            ->having('distance', '<', $radius)
            ->first();

        if (!is_null($r)) {
            return new LocationCache($r->code, $r->lat, $r->lng);
        } else {
            return null;
        }
    }

    public function cacheLocation(LocationCache $location)
    {
        $this->db->table('location_cache')
            ->insert([
                'code' => $location->code,
                'lat' => $location->lat,
                'lng' => $location->lng,
            ]);
    }

    private function getCacheId($code, $year, $month): string
    {
        return $code . '.' . $year . '.' . $month;
    }

}
