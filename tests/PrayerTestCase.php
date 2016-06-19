<?php

abstract class PrayerTestCase extends TestCase
{

    protected function getMockedProvider($y = null, $m = null)
    {
        $provider = Mockery::mock(\Mpt\Provider::class);

        if (!is_null($y)) {
            $provider->shouldReceive('setYear')
                ->with($y)
                ->atLeast()
                ->once()
                ->andReturnSelf();
        }

        if (!is_null($m)) {
            $provider->shouldReceive('setMonth')
                ->with($m)
                ->atLeast()
                ->once()
                ->andReturnSelf();
        }

        return $provider;
    }
}

class TestPrayerData extends \Mpt\Providers\Jakim\JakimPrayerData
{

    public function getMonth()
    {
        return 6;
    }

    public function getYear()
    {
        return 2016;
    }

    public function getCode()
    {
        return 'ext-308';
    }

    public function getTimes()
    {
        return [
            [1464730740, 1464735720, 1464758040, 1464770340, 1464780120, 1464784620],
            [1464817140, 1464822120, 1464844440, 1464856740, 1464866580, 1464871080],
            [1464903540, 1464908520, 1464930840, 1464943140, 1464952980, 1464957480],
            [1464990000, 1464994920, 1465017240, 1465029600, 1465039380, 1465043880],
            [1465076400, 1465081320, 1465103700, 1465116000, 1465125780, 1465130340],
            [1465162800, 1465167720, 1465190100, 1465202400, 1465212240, 1465216740],
            [1465249200, 1465254120, 1465276500, 1465288800, 1465298640, 1465303140],
            [1465335600, 1465340580, 1465362900, 1465375260, 1465385040, 1465389540],
            [1465422000, 1465426980, 1465449300, 1465461660, 1465471440, 1465476000],
            [1465508400, 1465513380, 1465535760, 1465548060, 1465557840, 1465562400],
            [1465594800, 1465599780, 1465622160, 1465634460, 1465644300, 1465648800],
            [1465681200, 1465686180, 1465708560, 1465720920, 1465730700, 1465735200],
            [1465767660, 1465772580, 1465794960, 1465807320, 1465817100, 1465821660],
            [1465854060, 1465859040, 1465881360, 1465893720, 1465903500, 1465908060],
            [1465940460, 1465945440, 1465967820, 1465980120, 1465989960, 1465994460],
            [1466026860, 1466031840, 1466054220, 1466066580, 1466076360, 1466080860],
            [1466113260, 1466118240, 1466140620, 1466152980, 1466162760, 1466167320],
            [1466199660, 1466204640, 1466227020, 1466239380, 1466249160, 1466253720],
            [1466286120, 1466291100, 1466313420, 1466325780, 1466335560, 1466340120],
            [1466372520, 1466377500, 1466399880, 1466412180, 1466422020, 1466426520],
            [1466458920, 1466463900, 1466486280, 1466498640, 1466508420, 1466512980],
            [1466545320, 1466550300, 1466572680, 1466585040, 1466594820, 1466599380],
            [1466631720, 1466636760, 1466659080, 1466671440, 1466681220, 1466685780],
            [1466718180, 1466723160, 1466745480, 1466757840, 1466767680, 1466772180],
            [1466804580, 1466809560, 1466831940, 1466844300, 1466854080, 1466858580],
            [1466890980, 1466895960, 1466918340, 1466930700, 1466940480, 1466945040],
            [1466977380, 1466982360, 1467004740, 1467017100, 1467026880, 1467031440],
            [1467063840, 1467068820, 1467091140, 1467103500, 1467113280, 1467117840],
            [1467150240, 1467155220, 1467177600, 1467189900, 1467199680, 1467204240],
            [1467236640, 1467241620, 1467264000, 1467276300, 1467286140, 1467290640]
        ];
    }

    public function getPlace()
    {
        return 'Kajang';
    }

    public function getProviderName()
    {
        return 'test';
    }

    public function getLastModified()
    {
        return new DateTime();
    }

    public function getJakimCode()
    {
        return 'sgr01';
    }

    public function getOriginCode()
    {
        return 'sgr-01';
    }

    public function getSource()
    {
        return 'http://www.e-solat.gov.my/web/muatturun.php?zone=sgr01&year=2016&bulan=6&jenis=year&lang=my&url=http://mpt.i906.my';
    }
}
