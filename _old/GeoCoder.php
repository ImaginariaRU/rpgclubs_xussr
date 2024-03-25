<?php

namespace _old;

use Arris\Entity\Result;
use GuzzleHttp\Exception\GuzzleException;
use maxh\Nominatim\Exceptions\NominatimException;
use maxh\Nominatim\Nominatim;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class GeoCoder extends AbstractClass
{
    /**
     * @var Nominatim
     */
    private Nominatim $geocoder;

    public function __construct($options = [], LoggerInterface $logger = null)
    {
        // parent::__construct($options, $logger);
        $this->geocoder = new Nominatim("http://nominatim.openstreetmap.org/");
    }

    /**
     * @throws GuzzleException
     * @throws NominatimException
     */
    public function getCityByCoords($lat, $lng): Result
    {
        $r = new Result();
        try {
            $reverse = $this->geocoder->newReverse()->latlon($lat, $lng);
            $found = $this->geocoder->find($reverse);

            $r->setData($found);
            $r->set('city', $found['address']['city']);
        } catch (\RuntimeException $e) {
            $r->error($e->getMessage());
        }

        return $r;
    }

    public function getCoordsByAddress($address)
    {
        $client = new \Nominatim\Client();
        try {
            $response = $client->search($address);
            if ($response->isOK()) {
                echo $response->getLat() . ", " . $response->getLng();
            } else {
                echo 'Location not found.';
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }



}