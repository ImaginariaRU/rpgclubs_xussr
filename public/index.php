<?php

use Arris\AppLogger;
use Arris\AppRouter;
use RPGCAtlas\App;
use RPGCAtlas\Common;
use RPGCAtlas\Exceptions\AccessDeniedException;

define('PATH_ROOT', dirname(__DIR__, 1));
define('ENGINE_START_TIME', microtime(true));
// define('PATH_ENV', '/etc/arris/rpgclubs/');
define('PATH_ENV', __DIR__ . '/../_config/');

if (!session_id()) @session_start();

error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

require_once PATH_ROOT . '/vendor/autoload.php';

try {
    foreach ([] as $file) \Dotenv\Dotenv::create(PATH_ENV, $file)->load();

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

    AppRouter::setDefaultNamespace("\FSNews\Controllers\Admin");

    AppRouter::get('/', [ MainController::class, 'view_main_page'], 'view.main.page');

    App::$template->assign("routing", AppRouter::getRoutersNames());

    AppRouter::dispatch();

    // App::$template->assign("title", App::$template->makeTitle(" &mdash;"));

    App::$template->assign("flash_messages", json_encode( App::$flash->getMessages() ));

    App::$template->assign("_auth", \config('auth'));
    App::$template->assign("_config", \config());
    App::$template->assign("_request", $_REQUEST);
} catch (AccessDeniedException $e) {

    AppLogger::scope('access.denied')->notice($e->getMessage(), [ $_SERVER['REQUEST_URI'], config('auth.ipv4') ] );
    App::$template->assign('message', $e->getMessage());
    App::$template->setTemplate("_errors/403.tpl");

} catch (\RuntimeException|\Exception $e) {
    // \Arris\Util\Debug::dump($e);
    d($_REQUEST);
    d($_SERVER['REQUEST_URI']);
    dd($e);
}

$render = App::$template->render();
if (!empty($render)) {
    App::$template->headers->send();
    echo $render;
}

Common::logSiteUsage( AppLogger::scope('site_usage') );

if (App::$template->isRedirect()) {
    App::$template->makeRedirect();
}