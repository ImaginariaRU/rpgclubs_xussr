<?php
/**
 * User: Arris
 * Date: 01.02.2018, time: 0:02
 */
// https://packagist.org/packages/pecee/simple-router

use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::setDefaultNamespace('RPGCAtlas\Units');

/* === FEEDBACK AJAX === */
//SimpleRouter::get   ('/ajax/feedback', 'Ajax@form_feedback');
//SimpleRouter::post  ('/ajax/feedback', 'Ajax@callback_feedback');


/* === AUTH === */


/* === PROFILE === */

SimpleRouter::group(['middleware' => _legacy\CheckAuth::class], function() {

});

/* === 404 === */
SimpleRouter::get('/404', 'Page@view_404')->name('page-404');
SimpleRouter::error(function(Pecee\Http\Request $request, \Exception $exception) {
    if($exception instanceof Pecee\SimpleRouter\Exceptions\NotFoundHttpException && $exception->getCode() == 404) {
        response()->redirect('/404');
    }
});