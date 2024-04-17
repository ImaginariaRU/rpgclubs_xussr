<?php

namespace RPGCAtlas\Units;

use Arris\Entity\Result;
use Dadata\DadataClient;

class GeoCoderDadata
{
    private DadataClient $geocoder;

    public function __construct()
    {
        $this->geocoder = new DadataClient(_env('GEOCODER.DADATA.TOKEN',''), _env('GEOCODER.DADATA.SECRET', ''));
    }

    public function getCoordsByAddress($address)
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
                'lon'   =>  $response['geo_lon'],
                'country'=> $response['country'],
                'city'  =>  $response['region']
            ]);

        } catch (\Exception $e) {
            $r->error($e->getMessage());
        }

        return $r;
    }

}