<?php

use Arris\AppLogger;
use Arris\AppRouter;
use Dotenv\Dotenv;
use RPGCAtlas\App;
use RPGCAtlas\Common;
use RPGCAtlas\Exceptions\AccessDeniedException;

define('PATH_ROOT', dirname(__DIR__, 1));
define('ENGINE_START_TIME', microtime(true));
const PATH_ENV = '/etc/arris/rpgclubs/';

if (!session_id()) @session_start();

error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    Dotenv::createUnsafeImmutable(PATH_ENV, ['site.conf'])->load();

    App::factory();

    App::init();

    App::initErrorHandler();

    App::initLogger();

    App::initTemplate();

    App::initMobileDetect();

    App::initDBConnection();

    App::initAuth();

    App::initRedis();

    App::addCustomServices();

    AppRouter::init(AppLogger::addScope('router'));

    AppRouter::setDefaultNamespace("\RPGCAtlas\Controllers");

    AppRouter::get('/', [ \RPGCAtlas\Controllers\MainController::class, 'view_main_page'], 'view.main.page');

    AppRouter::get('/ajax/poi:get/[{id}]', [ \RPGCAtlas\Controllers\AjaxController::class, 'view_poi_page'], 'ajax.view.poi.info');
    AppRouter::get('/ajax/poi:list/', [ \RPGCAtlas\Controllers\AjaxController::class, 'ajax_view_poi_list'], 'ajax.view.poi.list' );

    AppRouter::get('/places', [ \RPGCAtlas\Controllers\PlacesController::class, 'viewList'], 'view.poi.list');
    AppRouter::get('/places/add', [ \RPGCAtlas\Controllers\PlacesController::class, 'formAdd' ], 'form.add.poi');
    AppRouter::post('/places/insert', [ \RPGCAtlas\Controllers\PlacesController::class, 'callbackAdd' ], 'callback.add.poi');

    // сообщить о неточности о месте (2 энтрипоинта) - жалоба без ID - абстрактная, например запрос на добавление клуба (?)
    AppRouter::get('/places/complain/[{id}]', [ \RPGCAtlas\Controllers\TicketsController::class, 'formAdd'], 'form.add.ticket'); // вместо EDIT
    AppRouter::post('/places/complain', [ \RPGCAtlas\Controllers\TicketsController::class, 'callbackAdd'], 'callback.add.ticket'); // вместо EDIT

    // авторизация
    AppRouter::get('/auth/', function () {
        App::$template->setRedirect(AppRouter::getRouter('view.form.login'));
    });
    AppRouter::get('/auth/login[/]', [ \RPGCAtlas\Controllers\AuthController::class, 'view_form_login'], 'view.form.login');
    AppRouter::post('/auth/login[/]', [ \RPGCAtlas\Controllers\AuthController::class, 'callback_login'], 'callback.form.login');
    AppRouter::get('/auth/logout[/]', [ \RPGCAtlas\Controllers\AuthController::class, 'callback_logout'], 'view.form.logout');

    AppRouter::group(
        [
            'before'    =>  '\RPGCAtlas\Middlewares\AuthMiddleware@check_is_logged_in'
        ], static function() {

            AppRouter::group(['prefix' => '/admin'], static function(){
                AppRouter::get('', [ \RPGCAtlas\Controllers\AdminController::class, 'view_admin_page_main'], 'view.admin.page.main');

                // тикеты
                AppRouter::get('/tickets', [ \RPGCAtlas\Controllers\TicketsController::class, 'viewList'], 'view.ticket.list'); // список
                AppRouter::get('/tickets/view/{id}', [ \RPGCAtlas\Controllers\TicketsController::class, 'formView'], 'form.ticket.view'); // просмотр
                AppRouter::post('/tickets/update', [ \RPGCAtlas\Controllers\TicketsController::class, 'callbackUpdate'], 'callback.ticket.update'); // обновление (включая статус), удалить тикет нельзя

                // типы мест (иконки)
                AppRouter::get('/poi_types', [], 'view.poi_types.list');
                AppRouter::get('/poi_types/add', []);
                AppRouter::post('/poi_types/insert', []);
                AppRouter::get('/poi_types/edit/{id}', []);
                AppRouter::post('/poi_types/update', []); // удалить тип нельзя
            });

            AppRouter::get('/places/edit/[{id:\d+}]', [ \RPGCAtlas\Controllers\PlacesController::class, 'formEdit' ], 'form.edit.poi');
            AppRouter::post('/places/update', [ \RPGCAtlas\Controllers\PlacesController::class, 'callbackUpdate' ], 'callback.edit.poi');

            AppRouter::get('/places/delete/{id:\d+}', [ \RPGCAtlas\Controllers\PlacesController::class, 'callbackDelete' ], 'callback.delete.poi'); // удаление

                /* аякс-запросы для формы добавления клуба */
            AppRouter::get('/ajax/get:city:by:coords', [ \RPGCAtlas\Controllers\AjaxController::class, 'get_city_by_coords'], 'ajax.get_city_by_coords' );
            AppRouter::get('/ajax/get:coords:by:address', [ \RPGCAtlas\Controllers\AjaxController::class, 'get_coords_by_address'], 'ajax.get_coords_by_address');
            AppRouter::get('/ajax/get:vk:club:info', [ \RPGCAtlas\Controllers\AjaxController::class, 'get_vk_club_info'], 'ajax.get_vk_club_info');

            AppRouter::get('/ajax/get:poi:types', [ \RPGCAtlas\Controllers\AjaxController::class, 'get_poi_types'], 'ajax.get_poi_types'); // Список параметров иконок для селекта (на будущее)

            // пользователи?
        }
    );

    AppRouter::dispatch();

    // App::$template->assign("title", App::$template->makeTitle(" &mdash;"));

    App::$template->assign("routing", AppRouter::getRoutersNames());
    App::$template->assign("flash_messages", json_encode( App::$flash->getMessages() ));

    App::$template->assign("_auth", \config('auth'));
    App::$template->assign("_config", \config());
    App::$template->assign("_request", $_REQUEST);
    App::$template->assign("is_can_edit", App::$auth->isLoggedIn());


    \RPGCAtlas\TemplateHelper::init();
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

