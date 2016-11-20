<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\PrayerRequest;
use League\Fractal\TransformerAbstract;
use Mpt\Exception\ConnectException;
use Mpt\Exception\DataNotAvailableException;
use Mpt\Exception\InvalidCodeException;
use Mpt\Exception\InvalidDataException;
use Mpt\Exception\ProviderException;
use Mpt\Model\PrayerData;
use Mpt\Provider;

class PrayerController extends ApiController
{
    private $provider;
    private $sentry;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        $this->sentry = app('sentry');
    }

    public function code(PrayerRequest $request, $code)
    {
        try {
            $data = $this->provider
                ->setYear($request->getYear())
                ->setMonth($request->getMonth())
                ->getTimesByCode($code);

            return $this->produceResponse($request, $data);
        } catch (InvalidCodeException $e) {
            $this->response->errorNotFound("Unknown place code ($code) is used.");
        } catch (InvalidDataException $e) {
            $this->response->error($e->getMessage(), 502);
        } catch (ConnectException $e) {
            $this->response->error($e->getMessage(), 504);
        } catch (ProviderException $e) {
            $this->response->error('Error occured in provider: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            $this->response->error($e->getMessage(), 500);
        }

        return $this->response->noContent();
    }

    public function coordinate(PrayerRequest $request, $lat, $lng)
    {
        try {
            $data = $this->provider
                ->setYear($request->getYear())
                ->setMonth($request->getMonth())
                ->getTimesByCoordinates($lat, $lng);

            return $this->produceResponse($request, $data);
        } catch (DataNotAvailableException $e) {
            $this->sentry->captureMessage('Unsupported coordinates %s,%s', [$lat, $lng], [
                'extra' => ['coordinates' => "$lat,$lng"]
            ]);
            $this->response->errorNotFound("No provider support found for coordinate ($lat, $lng).");
        } catch (InvalidDataException $e) {
            $this->response->error($e->getMessage(), 502);
        } catch (ConnectException $e) {
            $this->response->error($e->getMessage(), 504);
        } catch (ProviderException $e) {
            $this->response->error('Error occured in provider: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            $this->response->error($e->getMessage(), 500);
        }

        return $this->response->noContent();
    }

    private function produceResponse(PrayerRequest $request, PrayerData $data)
    {
        $response = $this->response->item($data, new PrayerTransformer());

        $response->setLastModified($data->getLastModified())
            ->setEtag(md5($response->getContent()), true)
            ->isNotModified($request);

        return $response;
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
            'attributes' => $p->getExtraAttributes(),
            'times' => $p->getTimes(),
        ];
    }
}
