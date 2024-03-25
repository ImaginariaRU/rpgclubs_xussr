<?php

namespace _old\Middlewares;

use _old\Exceptions\AccessDeniedException;
use Arris\DelightAuth\Auth\Role;
use RPGCAtlas\App;

class AuthMiddleware
{
    /**
     * @param $uri
     * @param $route_info
     * @return void
     */
    public function check_is_logged_in($uri, $route_info)
    {
        if (!App::$auth->isLoggedIn()) {
            throw new AccessDeniedException("Вы не авторизованы. <br><br>Возможно, истекла сессия авторизации.");
        }
    }

    /**
     * @param $uri
     * @param $route_info
     * @return void
     */
    public function check_is_admin_logged($uri, $route_info)
    {
        if (!App::$auth->isLoggedIn() && App::$auth->hasRole(Role::ADMIN)) {
            throw new AccessDeniedException("У вас недостаточный уровень допуска. <br><br>Возможно, истекла сессия авторизации.");
        }

    }

}