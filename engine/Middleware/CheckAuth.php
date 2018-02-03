<?php
/**
 * User: Arris
 *
 * Class CheckAuth
 * Namespace: RPGCAtlas\Middleware
 *
 * Date: 03.02.2018, time: 19:28
 */

namespace RPGCAtlas\Middleware;

use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class CheckAuth implements IMiddleware {

    public function handle(Request $request) {

        // Authenticate user, will be available using request()->user
        // $request->user = User::authenticate();

        return $request; //@todo: после появления аутентификации добавить обработку посредника

        // If authentication failed, redirect request to user-login page.
        if((!$flag) || ($request->user === null)) {
            $request->setRewriteUrl( url('user.login') );
            return $request;
        }

    }
}