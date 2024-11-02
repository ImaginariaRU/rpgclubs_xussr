<?php

namespace RPGCAtlas\Units;

use Arris\Entity\Result;
use Dadata\DadataClient;

class GeoCoderDadata
{
    private DadataClient $geocoder;

    public function __construct($token = '', $secret = '')
    {
        $this->geocoder = new DadataClient(_env('GEOCODER.DADATA.TOKEN',$token), _env('GEOCODER.DADATA.SECRET', $secret));
    }

    /**
     * Возвращает город по координатам
     *
     * @param $lat
     * @param $lng
     * @return Result
     */
    public function getCityByCoords($lat, $lng):Result
    {
        $r = new Result();

        try {
            $response = $this->geocoder->geolocate("address", $lat, $lng, radiusMeters: 1, count: 1);

            if (empty($response)) {
                throw new \Exception("Empty result");
            }

            $response = array_shift($response);

            $r->city
                = array_key_exists('city', $response['data'])
                ? $response['data']['city']
                : $response['value'];

        } catch (\Exception $e) {
            $r->error($e->getMessage());
        }

        return $r;
    }

    /**
     * Возвращает геокоординаты и прочие данные по адресу
     *
     * @param $address
     * @return Result
     */
    public function getCoordsByAddress($address):Result
    {
        $r = new Result();

        try {
            $response = $this->geocoder->clean("address", $address);

            if (empty($response)) {
                throw new \Exception("Empty result");
            }

            if ($response['qc_geo'] == 5) {
                throw new \Exception("Координаты не определены");
            }

            $r->setData([
                '_'     =>  $response,
                'lat'   =>  $response['geo_lat'],
                'lng'   =>  $response['geo_lon'],
                'country'=> $response['country'],
                'city'  =>  $response['region']
            ]);

        } catch (\Exception $e) {
            $r->error($e->getMessage());
        }

        return $r;
    }

}