<?php

namespace Mpt;

use Illuminate\Cache\Repository;
use Illuminate\Database\Connection;
use Mpt\Model\LocationCache;
use Mpt\Model\PrayerData;
use Mpt\Model\UnsupportedLocationCache;

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

    private function getDistanceColumn($lat, $lng)
    {
        return "6371 * acos(" .
            "cos(radians($lat)) * " .
            "cos(radians(`lat`)) * " .
            "cos(radians(`lng`) - radians($lng)) + " .
            "sin(radians($lat)) * " .
            "sin(radians(`lat`))" .
            ") AS distance";
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
        $distance = $this->getDistanceColumn($lat, $lng);

        $r = $this->db->table('location_cache')
            ->select()
            ->selectRaw($distance)
            ->having('distance', '<', $radius)
            ->orderBy('distance')
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

    /**
     * @param $lat
     * @param $lng
     * @param int $radius
     * @return UnsupportedLocationCache|null
     */
    public function getNearestUnsupportedLocation($lat, $lng, $radius = 50)
    {
        $distance = $this->getDistanceColumn($lat, $lng);

        $r = $this->db->table('unsupported_location_cache')
            ->select()
            ->selectRaw($distance)
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->first();

        if (!is_null($r)) {
            return new UnsupportedLocationCache($r->lat, $r->lng, json_decode($r->locations));
        } else {
            return null;
        }
    }

    public function cacheUnsupportedLocation(UnsupportedLocationCache $location)
    {
        $this->db->table('unsupported_location_cache')
            ->insert([
                'lat' => $location->lat,
                'lng' => $location->lng,
                'locations' => json_encode($location->locations)
            ]);
    }
}
