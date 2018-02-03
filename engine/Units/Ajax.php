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
    public function get_poi_info($id) {
        return "AJAX return poi info {$id}";
    }

    public function form_feedback(){
        return "AJAX feedback form";
    }

    public function callback_feedback() {
        return "AJAX feedback form callback";
    }


}