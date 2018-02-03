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

class Page
{
    public function view_frontpage() {
        $template = new Template('frontpage.html', '$/templates/frontpage');

        $ip = (StaticConfig::get('global/server') == 'towers') ? '188.143.207.215' : getIp();
        $coords = getCoordsByIP($ip);

        $template->set('center', $coords);
        $template->set('section/infobox_position', 'topleft');
        $template->set('section/about_position', 'topright');

        return $template->render();
    }

    public function view_404() {
        echo "404";
    }

}