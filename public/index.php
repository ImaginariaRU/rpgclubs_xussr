<?php

use Arris\AppLogger;
use Arris\AppRouter;
use Dotenv\Dotenv;
use RPGCAtlas\App;
use RPGCAtlas\Common;
use RPGCAtlas\Controllers\AdminController;
use RPGCAtlas\Controllers\AjaxController;
use RPGCAtlas\Controllers\AuthController;
use RPGCAtlas\Controllers\MainController;
use RPGCAtlas\Controllers\PublicFormController;
use RPGCAtlas\Exceptions\AccessDeniedException;
use RPGCAtlas\Middlewares\AuthMiddleware;

define('PATH_ROOT', dirname(__DIR__, 1));
define('ENGINE_START_TIME', microtime(true));
define('PATH_ENV', '/etc/arris/rpgclubs/');

if (!session_id()) @session_start();

error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    Dotenv::createUnsafeImmutable(PATH_ENV, ['site.conf'])->load();

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
    AppRouter::get('/ajax/poi:get/[{id}]', [ AjaxController::class, 'view_poi_page'], 'ajax.view.poi.info');
    AppRouter::get('/ajax/poi:list/', [ AjaxController::class, 'ajax_view_poi_list'], 'ajax.view.poi.list' );


    AppRouter::get('/places/list', [], 'список мест');
    AppRouter::get('/places/add', [], 'форма: добавить место');
    AppRouter::post('/places/add', [], 'коллбэк: добавить место');

    AppRouter::get('/auth/login[/]', [ AuthController::class, 'view_form_login'], 'view.form.login');
    AppRouter::post('/auth/login[/]', [ AuthController::class, 'callback_login'], 'callback.form.login');
    AppRouter::get('/auth/logout[/]', [ AuthController::class, 'callback_logout'], 'view.form.logout');


    AppRouter::group(
        [
            'before'    =>  '\Confmap\Middlewares\AuthMiddleware@check_is_logged_in'
        ], static function() {

        AppRouter::get('/places/delete', []); // удаление

            /* аякс-запросы для формы добавления клуба */
        AppRouter::get('/ajax/get:city:by:coords', [ AjaxController::class, 'get_city_by_coords'], 'ajax_get_city_by_coords' );
        AppRouter::get('/ajax/get:coords:by:address', [ AjaxController::class, 'get_coords_by_address'], 'ajax_get_coords_by_address');
        AppRouter::get('/ajax/get:vk:club:info', [ AjaxController::class, 'get_vk_club_info'], 'ajax_get_vk_club_info');


        AppRouter::group([
            'prefix'    =>  '/admin'
        ], static function() {

            AppRouter::get  ('', [ AdminController::class, 'view_admin_page_main'], 'view.admin.page.main'); // главная страница админки

        });


    }
    );


    // AppRouter::get  ('/add', [ PublicFormController::class, 'view_form_poi_add'], 'view.form.add.poi'); // form_unauth_add_vk_club
    // AppRouter::post ('/pend', [ PublicFormController::class, 'callback_club_add'], 'callback.form.add.poi'); // callback_unauth_add_vk_club

    // аякс-методы, общие для добавления/удаления POI

    //@todo

    // Auth (login). Не закрываем

    // функционал добавления букмарки на карту (клуба?)
    // коллбэк добавления (


    /*AppRouter::group([
        'prefix'    =>  '/admin',
        'before'    =>  [ \RPGCAtlas\Middlewares\AuthMiddleware::class, 'check_is_logged_in'],
    ], static function() {
        AppRouter::get  ('/poi:types', [ AdminController::class, 'view_admin_page_types'], 'view.admin.page.types'); // типы POI

        // форма добавления POI
        // коллбэкп добавления POI

        // форма редактирования
        // коллбэк обновления
        // коллбэк удаления

        // форма добавления типа POI
        // коллбэк добавления типа POI
        // форма редактирования типа POI
        // коллбэк обновления типа POI
        // коллбэк удаления типа POI
        // аякс запрос на получение списка POI для селекта

        // пользователи?

        // статистика?
    });*/

    AppRouter::dispatch();

    // App::$template->assign("title", App::$template->makeTitle(" &mdash;"));

    App::$template->assign("routing", AppRouter::getRoutersNames());
    App::$template->assign("flash_messages", json_encode( App::$flash->getMessages() ));

    App::$template->assign("_auth", \config('auth'));
    App::$template->assign("_config", \config());
    App::$template->assign("_request", $_REQUEST);
    \RPGCAtlas\TemplateHelper::assignInnerButtons();

    $render = App::$template->render();
    if (!empty($render)) {
        App::$template->headers->send();

        $render = \preg_replace('/^\h*\v+/m', '', $render); // удаляем лишние переводы строк

        echo $render;
    }

    Common::logSiteUsage( AppLogger::scope('site_usage') );

    if (App::$template->isRedirect()) {
        App::$template->makeRedirect();
    }

} catch (AccessDeniedException $e) {

    AppLogger::scope('access.denied')->notice($e->getMessage(), [ $_SERVER['REQUEST_URI'], config('auth.ipv4') ] );
    App::$template->assign('message', $e->getMessage());
    App::$template->setTemplate("_errors/403.tpl");

} catch (\RuntimeException|\Exception $e) {
    \Arris\Util\Debug::dump($e);
    d($_REQUEST);
    d($_SERVER['REQUEST_URI']);
    dd($e);
}

