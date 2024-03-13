<?php

use Arris\AppLogger;
use Arris\AppRouter;
use RPGCAtlas\App;
use RPGCAtlas\Common;
use RPGCAtlas\Controllers\AjaxController;
use RPGCAtlas\Controllers\MainController;
use RPGCAtlas\Exceptions\AccessDeniedException;

define('PATH_ROOT', dirname(__DIR__, 1));
define('ENGINE_START_TIME', microtime(true));
define('PATH_ENV', '/etc/arris/rpgclubs/');

if (!session_id()) @session_start();

error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

try {
    require_once __DIR__ . '/../vendor/autoload.php';

    foreach (['site.conf'] as $file) \Dotenv\Dotenv::create(PATH_ENV, $file)->load();

    $app = App::factory();

    App::init();

    App::initErrorHandler();

    App::initLogger();

    App::initManticore();

    App::initTemplate();

    App::initMobileDetect();

    App::initDBConnection();

    App::initAuth();

    App::initRedis();

    App::addCustomServices();

    AppRouter::init(AppLogger::addScope('router'));

    AppRouter::setDefaultNamespace("\RPGCAtlas\Controllers");

    AppRouter::get('/', [ MainController::class, 'view_main_page'], 'view.main.page');

    /**
     * AJAX-запросы
     */
    AppRouter::get  ('/ajax/poi:get/[{id}]', [ AjaxController::class, 'view_poi_page'], 'ajax.view.poi.info');
    AppRouter::get  ('/ajax/poi:list/', [ AjaxController::class, 'ajax_view_poi_list'], 'ajax.view.poi.list' );

    AppRouter::get  ('/список', [ MainController::class, 'view_poi_list' ], 'view.poi.list');

    AppRouter::get  ('/ajax/get:city:by:coords', [ AjaxController::class, 'get_city_by_coords'], 'ajax_get_city_by_coords' );
    AppRouter::get  ('/ajax/get:vk:club:info', [ AjaxController::class, 'get_vk_club_info'], 'ajax_get_vk_club_info');
    AppRouter::get  ('/ajax/get:coords:by:address', [ AjaxController::class, 'get_coords_by_address'], 'ajax_get_coords_by_address');

    /*
     * Публичная форма добавления клуба
     */
    // легаси?
    AppRouter::get  ('/public/add_any_club', [ PublicForm::class, '']); // form_unauth_add_any_club
    AppRouter::post ('/public/add_any_club', [ PublicForm::class, '']); // callback_unauth_add_any_club

    // используемая
    AppRouter::get  ('/public/add_vk_club', [ PublicForm::class, '']); // form_unauth_add_vk_club
    AppRouter::post ('/public/add_vk_club', [ PublicForm::class, '']); // callback_unauth_add_vk_club

    // Auth (login)
    AppRouter::get   ('/auth/login', 'Auth@form_login', 'auth_form_login');
    AppRouter::post  ('/auth/login', 'Auth@callback_login', 'auth_callback_login');

    AppRouter::get   ('/auth/logout', 'Auth@form_logout', 'auth_form_logout');
    AppRouter::post  ('/auth/logout', 'Auth@callback_logout', 'auth_callback_logout');

    AppRouter::group([
        'before'    =>  '\RPGCAtlas\Middlewares\AuthMiddleware@check_logged_in',
        'prefix'    =>  '/admin'
    ], static function() {
        AppRouter::get   ('', [ ProfileController::class, 'view'], 'profile_view');
        AppRouter::get   ('/edit', 'Profile@form_edit', 'profile_form_edit');
        AppRouter::post  ('/edit', 'Profile@callback_edit', 'profile_callback_edit');

        AppRouter::get   ('/clubs', 'Clubs@view_clubs', 'admin_clubs_list');

        AppRouter::get   ('/clubs/add', 'Clubs@form_club_add', 'club_form_add');
        AppRouter::post  ('/clubs/add', 'Clubs@callback_club_add', 'club_callback_add');

        AppRouter::get   ('/clubs/edit/{id}', 'Clubs@form_club_edit', 'club_form_edit');
        AppRouter::post  ('/clubs/edit/{id}', 'Clubs@callback_club_edit', 'club_callback_edit');

        AppRouter::get   ('/clubs/delete/{id}', 'Clubs@callback_club_delete', 'club_callback_delete');
        AppRouter::get   ('/clubs/toggle/{id}', 'Clubs@callback_club_visibility_toggle', 'club_toggle_callback');


    });

    AppRouter::dispatch();

    // App::$template->assign("title", App::$template->makeTitle(" &mdash;"));

    App::$template->assign("routing", AppRouter::getRoutersNames());
    App::$template->assign("flash_messages", json_encode( App::$flash->getMessages() ));

    App::$template->assign("_auth", \config('auth'));
    App::$template->assign("_config", \config());
    App::$template->assign("_request", $_REQUEST);

} catch (AccessDeniedException $e) {

    AppLogger::scope('access.denied')->notice($e->getMessage(), [ $_SERVER['REQUEST_URI'], config('auth.ipv4') ] );
    App::$template->assign('message', $e->getMessage());
    App::$template->setTemplate("_errors/403.tpl");

}/* catch (\RuntimeException|\Exception $e) {
    // \Arris\Util\Debug::dump($e);
    var_dump($e);
    d($_REQUEST);
    d($_SERVER['REQUEST_URI']);
    dd($e);
}*/

$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();

    // return preg_replace('/^\h*\v+/m', '', $template->render()); - ?

    echo $render;
}

Common::logSiteUsage( AppLogger::scope('site_usage') );

if (App::$template->isRedirect()) {
    App::$template->makeRedirect();
}