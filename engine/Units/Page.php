<?php
/**
 * User: Arris
 *
 * Class Frontpage
 * Namespace: RPGCAtlas\Units
 *
 * Date: 03.02.2018, time: 18:44
 */

namespace RPGCAtlas\Units;

use PHPAuth\Config;
use RPGCAtlas\Classes\StaticConfig;
use RPGCAtlas\Classes\Template;
use RPGCAtlas\Classes\DBStatic;

class Page
{
    public function view_frontpage() {
        $template = new Template('frontpage.html', '$/templates/frontpage');

        // $ip = (StaticConfig::get('connection/suffix') == 'development') ? '188.143.207.215' : getIp();
        // $coords_latlng = getCoordsByIP($ip);
        // $location = getCityByCoords($coords_latlng['lat'], $coords_latlng['lng']);

        $city_location = [
            'zoom'  =>  4
        ];
        $ip_location = [
            'lat'   =>  56.769540,
            'lng'   =>  60.334709,
            'city'  =>  NULL
        ];

        // load clubs
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';
        $query = "
SELECT `id`, `lat`, `lng`
FROM {$table}
WHERE `is_public` = 1 AND `lat` IS NOT NULL AND `lng` IS NOT NULL
ORDER BY `id`";

        $dataset = $dbi->getConnection()->query($query)->fetchAll();

        $template->set('location', [
            'ip_lat'    =>  $ip_location['lat'],
            'ip_lng'    =>  $ip_location['lng'],
            'zoom'      =>  $city_location['zoom'],

            'city'      =>  $city_location['city']  ?? NULL,
            'city_lat'  =>  $city_location['city_lat'] ?? NULL,
            'city_lng'  =>  $city_location['city_lng'] ?? NULL
        ]);

        $template->set('center', $ip_location);

        $template->set('publish_options', [
            'assets_type'   =>  StaticConfig::get('global/server'),
            'allow_donate'  =>  StaticConfig::get('frontpage/allow_donate')
        ]);

        $template->set('href', [
            'unauth_add_any_club'   =>  url('club_form_unauth_add_any_club'),
            'unauth_add_vk_club'    =>  url('club_form_unauth_add_vk_club'),

            'admin_clubs_list'      =>  url('admin_clubs_list'),
            'public_clubs_list'     =>  url('public_clubs_list'),
            'colorbox_clubs_list'   =>  url('public_clubs_list_colorbox')
        ]);

        $template->set('dataset_clubs_list', $dataset);

        $template->set('section', [
            'infobox_position'  =>  'topleft',
            'about_position'    =>  'topright'
        ]);

        // Get Map Provider
        $provider_key = StaticConfig::get('map/use');
        $provider_data = StaticConfig::get("{$provider_key}");

        $template->set('map_provider', [
            'key'   =>  $provider_key,
            'href'  =>  StaticConfig::get("{$provider_key}/href"),
            'zoom'  =>  StaticConfig::get("{$provider_key}/zoom", 19),
            'attribution'   =>  StaticConfig::get("{$provider_key}/attribution")
        ]);

        return preg_replace('/^\h*\v+/m', '', $template->render());
    }

    public function view_404() {
        echo "404";
    }

}