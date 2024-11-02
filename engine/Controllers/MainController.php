<?php

namespace RPGCAtlas\Controllers;

use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;
use RPGCAtlas\Common;
use RPGCAtlas\MapProviders;
use RPGCAtlas\Units\POI;

class MainController extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);

        $this->template->setTemplate("_main.public.tpl");
    }

    public function view_main_page()
    {
        // detect location by IP
        $ip_location = [
            'lat'   =>  56.769540,
            'lng'   =>  60.334709,
        ];
        $city_location = [
            'city_lat'  =>  0,
            'city_lng'  =>  0,
            'zoom'  =>  4
        ];
        $ip_location = Common::getCoordsByIP(Server::getIP());

        $this->template->assign("location", [
            'ip_lat'    =>  $ip_location['lat'],
            'ip_lng'    =>  $ip_location['lng'],
            'zoom'      =>  4 /*$city_location['zoom']*/,

            'city'     =>   $ip_location['city'],

            // 'city'      =>  $city_location['city']  ?? 'Center',
            /*'city_lat'  =>  $city_location['city_lat'],
            'city_lng'  =>  $city_location['city_lng']*/
        ]);

        $this->template->assign('publish_options', [
            'allow_donate'  =>  _env('FRONTEND.ALLOW_DONATE', 0)
        ]);

        // для десктопа будем показывать секции, для мобилки - colorbox
        $this->template->assign("publish_options", [
            'is_mobile' =>  config('features.is_mobile.device')
        ]);

        $this->template->assign('section', [
            'infobox_position'  =>  'topleft',
            'about_position'    =>  'topright'
        ]);

        $use_map_provider = getenv('MAP.USE');

        $this->template->assign("map_provider", [
            'use'           =>  $use_map_provider,
            'href'          =>  MapProviders::PROVIDERS[ $use_map_provider ]['href'],
            'attribution'   =>  MapProviders::PROVIDERS[ $use_map_provider ]['attr'],
            'zoom'          =>  getenv('MAP.ZOOM')
        ]);

        /*
        $this->template->assign("features", [
            'yandex_metrika_enabled'    =>  _env('FRONTEND.METRIC.YANDEX', 0)
        ]);
        */

        $poi_dataset = (new POI())->getList();

        $this->template->assign('dataset_poi_list', $poi_dataset);

        $this->template->assign("summary", [
            'total'     =>  count($poi_dataset),
            'visible'   =>  count( array_filter($poi_dataset, static function($row) { return !!$row['is_public']; }) )
        ]);
    }

    /**
     * @return void
     */
    public function view_poi_list()
    {
        $dataset = (new POI())->getList();

        $this->template->assign('dataset', $dataset);
        $this->template->assign('summary', [
            'clubs_total'   =>  count($dataset),
            'clubs_visible' =>  count(array_filter($dataset, function($data){ return !!$data['is_public']; }) )
        ]);

        $this->template->setTemplate("ajax/poi_list.tpl");
    }

}