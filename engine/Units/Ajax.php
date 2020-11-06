<?php
/**
 * User: Arris
 *
 * Class Ajax
 * Namespace: RPGCAtlas\Units
 *
 * Date: 03.02.2018, time: 19:12
 */

namespace RPGCAtlas\Units;

use RPGCAtlas\Classes\DBStatic;
use RPGCAtlas\Classes\Template;

class Ajax
{
    public function get_info_poi($id)
    {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';
        $query = "SELECT * FROM {$table} WHERE `id` = :id ORDER BY `id` DESC LIMIT 1";

        $sth = $dbi->getConnection()->prepare($query);
        $sth->execute([
            'id'    =>  $id
        ]);
        $dataset = $sth->fetch();
        $dataset['title'] = htmlspecialchars($dataset['title'], ENT_QUOTES | ENT_HTML5);

        // $template_name = $dataset['infobox_layout'] == 'VKBanner' ? 'img_horizontal.html' : 'img_vertical.html';
        $template_name = 'info_banner_horizontal.html';

        $template = new Template($template_name, '$/templates/info');

        $template->set('dataset', $dataset);

        return $template->render();
    }

    public function get_city_by_coords()
    {
        $lat = input('lat');
        $lng = input('lng');
        $latlng = input('latlng');

        if (!($lat&&$lng) && ($latlng)) {
            $set = explode(', ', trim($latlng));
            $lat = $set[0];
            $lng = $set[1];
        }

        $city = getCityByCoords($lat, $lng)['city'];
        return $city;
    }

    public function form_feedback(){
        return "AJAX feedback form";
    }

    public function callback_feedback() {
        return "AJAX feedback form callback";
    }


    public function get_vk_club_info()
    {
        $club_id = input('club_id');

        $info = getVKGroupInfo( $club_id );

        return json_encode($info);
    }

    public function get_coords_by_address()
    {
        $address = input('club_address');

        $coords = getCoordsByAddress($address);

        return json_encode($coords);
    }

}