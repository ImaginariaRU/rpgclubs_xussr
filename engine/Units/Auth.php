<?php
/**
 * User: Arris
 *
 * Class Auth
 * Namespace: RPGCAtlas\Units
 *
 * Date: 03.02.2018, time: 18:44
 */

namespace RPGCAtlas\Units;

use RPGCAtlas\Classes\Template;

class Auth
{
    public function login_form() {
        $template = new Template('login.html', '$/templates/auth');

        $template->set('href', [
            'login_callback'    =>  url('auth_login_callback'),
            'frontpage'         =>  url('frontpage')
        ]);

        return $template->render();
    }

    public function login_callback() {
        return "Login callback";
    }

    public function logout_form() {
        return "Logout form";
    }

    public function logout_callback() {
        return "Logout callback";
    }
}