<?php
/**
 * User: Arris
 * Date: 01.02.2018, time: 0:02
 */
// https://packagist.org/packages/pecee/simple-router

use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::setDefaultNamespace('RPGCAtlas\Units');

/* === FRONTPAGE === */
SimpleRouter::get   ('/', 'Page@view_frontpage')->name('frontpage');

/* === AJAX: GET POI */
SimpleRouter::get   ('/ajax/poi/{id}', 'Ajax@get_poi_info')->name('get_poi_info');

/* === AJAX: FEEDBACK === */
SimpleRouter::get   ('/ajax/feedback', 'Ajax@form_feedback');
SimpleRouter::post  ('/ajax/feedback', 'Ajax@callback_feedback');

/* === AUTH === */
SimpleRouter::get   ('/auth/login', 'Auth@login_form');
SimpleRouter::post  ('/auth/login', 'Auth@login_callback')->name('auth_login_callback');

SimpleRouter::get   ('/auth/logout', 'Auth@logout_form');
SimpleRouter::post  ('/auth/logout', 'Auth@logout_callback')->name('auth_logout_callback');

/* === PROFILE === */

SimpleRouter::group(['middleware' => \RPGCAtlas\Middleware\CheckAuth::class], function() {

    SimpleRouter::get   ('/profile', 'Profile@view')->name('profile_view');
    SimpleRouter::get   ('/profile/edit', 'Profile@form_edit');

    SimpleRouter::get   ('/profile/clubs', 'Clubs@view_clubs')->name('clubs_list');

    SimpleRouter::get   ('/profile/clubs/add', 'Clubs@form_club_add')->name('club_add_form');
    SimpleRouter::post  ('/profile/clubs/add', 'Clubs@callback_club_add')->name('club_add_callback');

    SimpleRouter::get   ('/profile/clubs/edit/{id}', 'Clubs@form_club_edit');
    SimpleRouter::post  ('/profile/clubs/edit/{id}', 'Clubs@callback_club_edit');

    SimpleRouter::post  ('/profile/clubs/delete/{id}', 'Clubs@callback_club_delete');
    SimpleRouter::get   ('/profile/clubs/visibilty_toggle/{id}', 'Clubs@callback_club_visibility_toggle');
});

/* === 404 === */
SimpleRouter::get('/404', 'Page@view_404')->name('page-404');
SimpleRouter::error(function(Pecee\Http\Request $request, \Exception $exception) {
    if($exception instanceof Pecee\SimpleRouter\Exceptions\NotFoundHttpException && $exception->getCode() == 404) {
        response()->redirect('/404');
    }
});