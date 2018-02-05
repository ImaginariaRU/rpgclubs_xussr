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

        $template_name = $dataset['infobox_layout'] == 'VKBanner' ? 'img_horizontal.html' : 'img_vertical.html';

        $template = new Template($template_name, '$/templates/info');

        $template->set('dataset', $dataset);


        return $template->render();
    }

    public function get_city_by_coords()
    {
        $lat = input('lat');
        $lng = input('lng');

        $city = getCityByCoords($lat, $lng)['city'];
        return $city;
    }

    public function form_feedback(){
        return "AJAX feedback form";
    }

    public function callback_feedback() {
        return "AJAX feedback form callback";
    }


}