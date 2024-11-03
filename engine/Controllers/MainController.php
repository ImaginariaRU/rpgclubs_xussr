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
        $ip_location = Common::getCoordsByIP(Server::getIP());

        $this->template->assign("location", [
            'ip_lat'    =>  $ip_location['lat'],
            'ip_lng'    =>  $ip_location['lng'],
            'zoom'      =>  4 ,

            'city'     =>   $ip_location['city'],
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

}