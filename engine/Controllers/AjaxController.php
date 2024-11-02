<?php

namespace RPGCAtlas\Controllers;

use Arris\Core\Curl;
use Psr\Log\LoggerInterface;
use RPGCAtlas\Common;
use RPGCAtlas\Units\GeoCoderDadata;
use RPGCAtlas\Units\POI;
use stdClass;

class AjaxController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * @param $id
     * @return void
     */
    public function view_poi_page($id)
    {
        $poi = (new POI())->getItem($id);

        $poi['title'] = htmlspecialchars($poi['title'], ENT_QUOTES | ENT_HTML5);

        $this->template->assign('dataset', $poi);
        $this->template->setTemplate("public/ajax_poi_info.tpl");
    }

    public function ajax_view_poi_list()
    {
        $dataset = (new POI())->getList();

        $this->template->assign('dataset', $dataset);
        $this->template->assign('summary', [
            'clubs_total'   =>  count($dataset),
            'clubs_visible' =>  count(array_filter($dataset, function($data){ return !!$data['is_public']; }) )
        ]);

        $this->template->setTemplate("public/ajax_poi_list.tpl");
    }

    public function get_coords_by_address()
    {
        $address = input('poi_address');

        $geocoder = new GeoCoderDadata();

        $result = $geocoder->getCoordsByAddress($address);

        $this->template->assignResult($result);
    }

    public function get_city_by_coords()
    {
        $lat = input('lat');
        $lng = input('lng');

        $result = (new GeoCoderDadata())->getCityByCoords($lat, $lng);

        $this->template->assignResult($result);
    }

    public function get_vk_club_info()
    {
        $id = input('poi_id');

    }

    /**
     * @return array
     */
    public function get_coords_by_ip()
    {
        $ip = input('ip');

        return Common::getCoordsByIP($ip);
    }



}