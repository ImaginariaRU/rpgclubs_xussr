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

        $ip = (StaticConfig::get('global/server') == 'development') ? '188.143.207.215' : getIp();
        $coords = getCoordsByIP($ip);

        // load clubs
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';
        $query = "SELECT `id`, `lat`, `lng` FROM {$table} WHERE `is_public` = 1 ORDER BY `id`";

        $dataset = $dbi->getConnection()->query($query)->fetchAll();

        $template->set('head/assets', StaticConfig::get('global/server'));
        $template->set('clubs_list', $dataset);
        $template->set('center', $coords);
        $template->set('section/infobox_position', 'topleft');
        $template->set('section/about_position', 'topright');

        // $packer = new HtmlMin();
        // return $packer->minify($template->render());

        return preg_replace('/^\h*\v+/m', '', $template->render());
    }

    public function view_404() {
        echo "404";
    }

}