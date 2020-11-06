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

/* === AJAX */
SimpleRouter::get   ('/ajax/get:poi/{id}', 'Ajax@get_info_poi')->name('ajax_info_poi');
SimpleRouter::get   ('/ajax/get:city:by:coords', 'Ajax@get_city_by_coords')->name('ajax_get_city_by_coords');
SimpleRouter::get   ('/ajax/get:vk:club:info', 'Ajax@get_vk_club_info')->name('ajax_get_vk_club_info');
SimpleRouter::get   ('/ajax/get:coords:by:address', 'Ajax@get_coords_by_address')->name('ajax_get_coords_by_address');

/* === FEEDBACK AJAX === */
//SimpleRouter::get   ('/ajax/feedback', 'Ajax@form_feedback');
//SimpleRouter::post  ('/ajax/feedback', 'Ajax@callback_feedback');

/* exoterical - общедоступный, понятный непосвящённым. К сожалению, имя класса `public` невозможно */
/* Форма добавления клуба анонимусом */
SimpleRouter::get   ('/exoterical/add_any_club', 'Exoterical@form_unauth_add_any_club')->name('club_form_unauth_add_any_club');
SimpleRouter::post  ('/exoterical/add_any_club', 'Exoterical@callback_unauth_add_any_club')->name('club_callback_unauth_add_any_club');

SimpleRouter::get   ('/exoterical/add_vk_club', 'Exoterical@form_unauth_add_vk_club')->name('club_form_unauth_add_vk_club');
SimpleRouter::post  ('/exoterical/add_vk_club', 'Exoterical@callback_unauth_add_vk_club')->name('club_callback_unauth_add_vk_club');

SimpleRouter::get   ('/list', 'Exoterical@public_clubs_list')->name('public_clubs_list');
SimpleRouter::get   ('/list_colorbox', 'Exoterical@public_clubs_list_colorbox')->name('public_clubs_list_colorbox');

/* === AUTH === */
SimpleRouter::get   ('/auth/login', 'Auth@form_login')->name('auth_form_login');
SimpleRouter::post  ('/auth/login', 'Auth@callback_login')->name('auth_callback_login');

SimpleRouter::get   ('/auth/logout', 'Auth@form_logout')->name('auth_form_logout');
SimpleRouter::post  ('/auth/logout', 'Auth@callback_logout')->name('auth_callback_logout');

SimpleRouter::get   ('/auth/registration', 'Auth@form_registration')->name('auth_form_registration');
SimpleRouter::post  ('/auth/registration', 'Auth@callback_registration')->name('auth_callback_registration');

/* === PROFILE === */

SimpleRouter::group(['middleware' => \RPGCAtlas\Middleware\CheckAuth::class], function() {

    SimpleRouter::get   ('/profile', 'Profile@view')->name('profile_view');
    SimpleRouter::get   ('/profile/edit', 'Profile@form_edit')->name('profile_form_edit');
    SimpleRouter::post  ('/profile/edit', 'Profile@callback_edit')->name('profile_callback_edit');

    SimpleRouter::get   ('/profile/clubs', 'Clubs@view_clubs')->name('admin_clubs_list');

    SimpleRouter::get   ('/profile/clubs/add', 'Clubs@form_club_add')->name('club_form_add');
    SimpleRouter::post  ('/profile/clubs/add', 'Clubs@callback_club_add')->name('club_callback_add');

    SimpleRouter::get   ('/profile/clubs/edit/{id}', 'Clubs@form_club_edit')->name('club_form_edit');
    SimpleRouter::post  ('/profile/clubs/edit/{id}', 'Clubs@callback_club_edit')->name('club_callback_edit');

    SimpleRouter::get   ('/profile/clubs/delete/{id}', 'Clubs@callback_club_delete')->name('club_callback_delete');
    SimpleRouter::get   ('/profile/clubs/toggle/{id}', 'Clubs@callback_club_visibility_toggle')->name('club_toggle_callback');
});

/* === 404 === */
SimpleRouter::get('/404', 'Page@view_404')->name('page-404');
SimpleRouter::error(function(Pecee\Http\Request $request, \Exception $exception) {
    if($exception instanceof Pecee\SimpleRouter\Exceptions\NotFoundHttpException && $exception->getCode() == 404) {
        response()->redirect('/404');
    }
});