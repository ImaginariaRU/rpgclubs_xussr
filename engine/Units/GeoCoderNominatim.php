<?php

namespace RPGCAtlas\Units;

use Arris\Entity\Result;
use Nominatim\Client;
use Psr\Log\LoggerInterface;

class GeoCoderNominatim
{
    /**
     * @var
     */
    private $geocoder;

    public function __construct($options = [], LoggerInterface $logger = null)
    {
        $this->geocoder = new Client();
    }

    public function getCoordsByAddress($address):Result
    {
        $r = new Result();

        try {
            $response = $this->geocoder->search($address);

            if (!$response->isOK()) {
                throw new \Exception($response->getError());
            }

            $response = $response[0];
            $r->setData([
                '_'     =>  $response,
                'lat'   =>  $response['lat'],
                'lon'   =>  $response['lon'],
                'city'  =>  $response
            ]);

        } catch (\Exception $e) {
            $r->error($e->getMessage());
        }

        return $r;
    }


}