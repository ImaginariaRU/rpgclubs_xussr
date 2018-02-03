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

class Auth
{
    public function login_form() {
        return "Login form";
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