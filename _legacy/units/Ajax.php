<?php
/**
 * User: Arris
 *
 * Class Ajax
 * Namespace: RPGCAtlas\Units
 *
 * Date: 03.02.2018, time: 19:12
 */


use RPGCAtlas\Classes\DBStatic;
use RPGCAtlas\Classes\Template;

class Ajax
{


    public function get_city_by_coords()
    {
        $lat = \RPGCAtlas\Units\input('lat');
        $lng = \RPGCAtlas\Units\input('lng');
        $latlng = \RPGCAtlas\Units\input('latlng');

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
        $club_id = \RPGCAtlas\Units\input('club_id');

        $info = getVKGroupInfo( $club_id );

        return json_encode($info);
    }

    public function get_coords_by_address()
    {
        $address = \RPGCAtlas\Units\input('club_address');

        $coords = getCoordsByAddress($address);

        return json_encode($coords);
    }

}