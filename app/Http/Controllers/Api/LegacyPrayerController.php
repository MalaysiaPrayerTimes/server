<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mpt\Exception\ConnectException;
use Mpt\Exception\InvalidCodeException;
use Mpt\Exception\InvalidDataException;
use Mpt\Provider;

class LegacyPrayerController extends Controller
{
    const ERROR_ESOLAT = 'ERROR_ESOLAT';
    const ERROR_INVALID_DATA = 'ERROR_INVALID_DATA';
    const ERROR_NO_PLACE = 'ERROR_NO_PLACE';
    const ERROR_UNKNOWN = 'ERROR_UNKNOWN';

    const FILTER_DAY = 1;
    const FILTER_WEEK = 2;
    const FILTER_MONTH = 3;

    private $manager;

    public function __construct(Provider $manager)
    {
        $this->manager = $manager;
    }

    public function getApi(Request $request)
    {
        $code = $request->input('code');
        $filter = $request->input('filter');
        $month = $request->input('month');
        $year = $request->input('year');

        if (empty($code)) {
            return $this->throwError(self::ERROR_NO_PLACE, 'No place code has been defined.');
        }

        try {
            $data = $this->manager
                ->setYear($year)
                ->setMonth($month)
                ->getTimesByCode($code);
        } catch (InvalidCodeException $e) {
            return $this->throwError(self::ERROR_NO_PLACE, "Unknown place code ($code) is used.");
        } catch (InvalidDataException $e) {
            return $this->throwError(self::ERROR_INVALID_DATA, $e->getMessage());
        } catch (ConnectException $e) {
            return $this->throwError(self::ERROR_ESOLAT, $e->getMessage());
        } catch (\Exception $e) {
            return $this->throwError(self::ERROR_UNKNOWN,
                "An unknown error has occurred. Contact the author. "
                . "Message: [" . get_class($e) . '] ' . $e->getMessage());
        }

        if ($filter == self::FILTER_DAY) {
            $times = $data->getTimes()[date('j') - 1];
        } else {
            if ($filter == self::FILTER_WEEK) {
                $f1 = date('w');
                $f2 = date('j', strtotime("-$f1 days"));
                $times = array_slice($data->getTimes(), $f2, 7);
            } else {
                $times = $data->getTimes();
            }
        }

        $response = response()
            ->json([
                'meta' => [
                    'code' => 200
                ],
                'response' => [
                    'provider' => $data->getProviderName(),
                    'code' => $data->getCode(),
                    'origin' => $data->getOriginCode(),
                    'jakim' => $data->getJakimCode(),
                    'source' => $data->getSource(),
                    'place' => $data->getPlace(),
                    'times' => $times
                ]
            ])
            ->setLastModified($data->getLastModified());

        $response->setEtag(md5($response->getContent()), true)
            ->isNotModified($request);

        return $response;
    }

    private function throwError($type, $details)
    {
        return response()
            ->json([
                'meta' => [
                    'code' => 400,
                    'errorType' => $type,
                    'errorDetail' => $details
                ],
                'response' => [
                    'error' => true
                ]
            ], 400);
    }
}
