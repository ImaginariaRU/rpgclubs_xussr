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
    /* === LOGIN === */
    public function form_login()
    {
        $template = new Template('login.html', '$/templates/auth');

        $template->set('href', [
            'form_action'    =>  url('auth_callback_login'),
            'frontpage'         =>  url('frontpage')
        ]);

        return $template->render();
    }

    public function callback_login()
    {
        return "Login callback";
    }

    /* === LOGOUT === */
    public function form_logout()
    {
        $template = new Template('logout.html', '$/templates/auth');

        $template->set('href', [
            'form_action'    =>  url('auth_callback_logout'),
            'frontpage'      =>  url('frontpage')
        ]);

        return $template->render();
    }

    public function callback_logout()
    {
        return "Logout callback";
    }



    /* === REGISTER FORM === */

    public function register_form()
    {
        $template = new Template('register.html', '$/templates/auth');

        $template->set('href', [
            'form_action'    =>  url('auth_register_callback'),
            'frontpage'      =>  url('frontpage')
        ]);

        return $template->render();
    }




}