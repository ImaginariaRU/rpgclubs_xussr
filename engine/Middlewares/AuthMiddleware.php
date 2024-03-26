<?php

namespace RPGCAtlas\Middlewares;

use Arris\AppRouter;
use Arris\DelightAuth\Auth\Role;
use RPGCAtlas\App;
use RPGCAtlas\Exceptions\AccessDeniedException;

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
            $uri_login = AppRouter::getRouter('view.form.login');

            throw new AccessDeniedException(<<<AUTH_LOST
Вы не авторизованы. <br><br>Возможно, истекла сессия авторизации. <br><br>
<a href="{$uri_login}"> На страницу логина</a> <br>
AUTH_LOST
            );
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