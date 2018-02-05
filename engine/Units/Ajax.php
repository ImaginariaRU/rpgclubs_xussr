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

class Ajax
{
    public function get_info_poi($id)
    {





        return "AJAX return poi info {$id}";
    }

    public function get_city_by_coords()
    {
        $lat = input('lat');
        $lng = input('lng');

        $city = getCityByCoords($lat, $lng);
        return $city;
    }

    public function form_feedback(){
        return "AJAX feedback form";
    }

    public function callback_feedback() {
        return "AJAX feedback form callback";
    }


}