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

use RPGCAtlas\Classes\StaticConfig;
use RPGCAtlas\Classes\Template;
use RPGCAtlas\Classes\DBStatic;

class Page
{
    public function view_frontpage() {
        $template = new Template('frontpage.html', '$/templates/frontpage');

        $ip = (StaticConfig::get('connection/suffix') == 'development') ? '188.143.207.215' : getIp();
        $coords_latlng = getCoordsByIP($ip);
        $location = getCityByCoords($coords_latlng['lat'], $coords_latlng['lng']);

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
            'ip_lat'    =>  $coords_latlng['lat'],
            'ip_lng'    =>  $coords_latlng['lng'],
            'city'      =>  $location['city']  ?? NULL,
            'city_lat'  =>  $location['city_lat'] ?? NULL,
            'city_lng'  =>  $location['city_lng'] ?? NULL
        ]);

        $template->set('center', $coords_latlng);

        $template->set('publish_options', [
            'assets_type'   =>  StaticConfig::get('global/server'),
            'allow_donate'  =>  StaticConfig::get('frontpage/allow_donate')
        ]);

        $template->set('href', [
            'unauth_add_any_club'   =>  url('club_form_unauth_add_any_club'),
            'unauth_add_vk_club'    =>  url('club_form_unauth_add_vk_club'),

            'admin_clubs_list'      =>  url('admin_clubs_list'),
            'public_clubs_list'     =>  url('public_clubs_list')
        ]);

        $template->set('dataset_clubs_list', $dataset);

        $template->set('section', [
            'infobox_position'  =>  'topleft',
            'about_position'    =>  'topright'
        ]);

        // $packer = new HtmlMin();
        // return $packer->minify($template->render());

        return preg_replace('/^\h*\v+/m', '', $template->render());
    }

    public function view_404() {
        echo "404";
    }

}