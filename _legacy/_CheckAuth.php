<?php
/**
 * User: Arris
 *
 * Class CheckAuth
 * Namespace: RPGCAtlas\Middleware
 *
 * Date: 03.02.2018, time: 19:28
 */

namespace _legacy;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use RPGCAtlas\Classes\StaticConfig;
use function RPGCAtlas\Middlewares\response;
use function RPGCAtlas\Middlewares\url;

class CheckAuth implements IMiddleware {

    public function handle(Request $request) {

        // сейчас доступность закрытого раздела определяется на основе подключения к БД
        if (StaticConfig::get('connection/suffix') == 'production') {
            // $request->setRewriteUrl( url('auth_form_login') );
            // return $request;
            response()->redirect( url('auth_form_login') );
        }

        // Authenticate user, will be available using request()->user
        // $request->user = User::authenticate();

        //@todo: после появления аутентификации добавить обработку посредника
        // return $request;

        // If authentication failed, redirect request to user-login page.
        /*if((!$flag) || ($request->user === null)) {
            $request->setRewriteUrl( url('user.login') );
            return $request;
        }*/

    }
}