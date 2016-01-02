<?php
declare(strict_types=1);

namespace Mpt;

use Cache;
use Mpt\Exception\InvalidCodeException;
use Mpt\Jakim\JakimPrayerData;
use Mpt\Jakim\JakimProvider;
use Mpt\PrayerData;

class PrayerTimesManager
{
    use DateTrait;

    private $providers = [];
    private $usedProvider = 'mpt';
    private $lastModified;

    public function __construct(JakimProvider $jp)
    {
        $this->providers[] = $jp;
        $this->lastModified = new \DateTime();
    }

    public function getUsedProviderName(): string
    {
        return $this->usedProvider;
    }

    public function getLastModified(): \DateTime
    {
        return $this->lastModified;
    }

    public function getCodeByCoordinates(float $lat, float $lng, int $acc = 0): string
    {
        // TODO: Implement getCodeByCoordinates() method.
    }

    public function getTimesByCode(string $code): PrayerData
    {
        $cache = $this->getCachedTimes($code);

        if (!is_null($cache)) {
            $this->usedProvider = $cache->provider;
            $this->lastModified = $cache->lastModified;
            return $cache->data;
        }

        foreach ($this->providers as $provider) {
            try {
                $this->usedProvider = $provider->getProviderName();

                $data = $provider->setYear($this->getYear())
                    ->setMonth($this->getMonth())
                    ->getTimesByCode($code);

                $this->saveTimesIntoCache($code, $data);
                return $data;
            } catch (InvalidCodeException $e) {
            }
        }

        throw new InvalidCodeException();
    }

    private function getCacheId(string $code): string
    {
        $y = $this->getYear();
        $m = $this->getMonth();
        return "$code-$y-$m";
    }

    private function getCachedTimes(string $code): CacheInfo
    {
        return Cache::get($this->getCacheId($code));
    }

    private function saveTimesIntoCache(string $code, PrayerData $times)
    {
        $cache = new CacheInfo($this->usedProvider, $times, new \DateTime());
        Cache::forever($this->getCacheId($code), $cache);
    }
}

class CacheInfo
{
    public $data;
    public $lastModified;
    public $provider;

    public function __construct($provider, $data, $date)
    {
        $this->data = $data;
        $this->lastModified = $date;
        $this->provider = $provider;
    }
}
