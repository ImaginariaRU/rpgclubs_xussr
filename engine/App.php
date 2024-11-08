<?php

namespace RPGCAtlas;

use Arris\AppLogger;
use Arris\Cache\Cache;
use Arris\Cache\CacheInterface;
use Arris\Database\DBWrapper;
use Arris\DelightAuth\Auth\Auth;
use Arris\DelightAuth\Auth\Exceptions\DatabaseError;
use Arris\DelightAuth\Auth\Role;
use Arris\Path;
use Arris\Template\FlashMessages;
use Arris\Template\Template;
use Kuria\Error\ErrorHandler;
use Kuria\Error\Screen\WebErrorScreen;
use Kuria\Error\Screen\WebErrorScreenEvents;

class App extends \Arris\App
{
    /**
     * @var Template
     */
    public static Template $template;

    /**
     * @var FlashMessages
     */
    public static FlashMessages $flash;

    /**
     * @var DBWrapper|\PDO
     */
    public static $pdo;

    /**
     * @var Auth
     */
    public static Auth $auth;

    public static function init()
    {
        $app = App::factory();

        $_path_install = Path::create( getenv('PATH.INSTALL') );
        $_path_monolog = Path::create( getenv('PATH.LOGS') );

        config('ENV_STATE', _env('ENV_STATE', 'dev'));

        config('path', [
            'install'           =>  $_path_install->toString(true),
            'public'            =>  $_path_install->join('public')->toString('/'),
            'cache'             =>  $_path_install->join('cache')->toString('/'),
            'monolog'           =>  $_path_monolog->toString(),
            'storage'           =>  getenv('PATH.STORAGE')
        ]);

/*        config('domains', [
            'scheme'    =>  getenv('SCHEME'),
            'site'      =>  getenv('DOMAIN'),
            'fqdn'      =>  getenv('DOMAIN.FQDN')
        ]);*/

        config('application.meta', [
            'keywords'          =>  'map, rpg, clubs, ролевые клубы, карта, настольные, ролевые, игры, поиграть, НРИ, антикафе, игротеки',
            'description'       =>  'Ролевые клубы на карте России и ближайшего зарубежья',
            'copyright'         =>  'Karel Wintersky, 2018-2024',
            'revised'           =>  '', // на стадии сборки патчим в файле шаблона %%application_meta_revised%% на дату сборки из чейнжлога
            'author'            =>  'Karel Wintersky, rpgclubsrf@yandex.ru',
            'title'             =>  'ролевыеклубы.рф',
            'title_sub'         =>  'Ролевые клубы на карте мира',
        ]);

        config('geo', [
            'default_zoom'  =>  5,
            'close_zoom'    =>  14,
            'location'  =>  [
                'maximumAge'    =>  10000,
                'detectionTimeout'  =>  30000,
            ],
        ]);

    }

    /**
     * @throws \Exception
     */
    public static function initLogger()
    {
        AppLogger::init("rpgClubs", bin2hex(random_bytes(4)), [
            'default_logfile_path'      =>  config('path.monolog'),
            'default_logfile_prefix'    =>  date_format(date_create(), 'Y-m') . '__'
        ]);
        AppLogger::addScope('main');

        AppLogger::addScope('auth', [
            [ 'auth.log', AppLogger\Monolog\Logger::NOTICE ]
        ]);

        AppLogger::addScope('site_usage', [
            [ 'site_usage.log', AppLogger\Monolog\Logger::NOTICE]
        ]);
    }

    /**
     * @throws \SmartyException
     */
    public static function initTemplate()
    {
        self::factory();

        config('smarty', [
            'path_template'     =>  config('path.public') . 'templates/',
            'path_cache'        =>  config('path.cache'),
            'force_compile'     =>  _env('DEBUG.SMARTY_FORCE_COMPILE', false, 'bool')
        ]);

        App::$template = new Template($_REQUEST, []);
        App::$template
            ->setTemplateDir( config('smarty.path_template') )
            ->setCompileDir( config('smarty.path_cache') )
            ->setForceCompile( config('smarty.force_compile') )
            ->registerPlugin( Template::PLUGIN_MODIFIER, 'dd', 'dd', false)
            ->registerPlugin( Template::PLUGIN_MODIFIER, "convertDateTime", [ \RPGCAtlas\Common::class, "convertDateTimeRU" ])

            // {_env key='' default=100};
            ->registerPlugin(Template::PLUGIN_FUNCTION, "_env", static function($params)
            {
                $default = (empty($params['default'])) ? '' : $params['default'];
                if (empty($params['key'])) return $default;
                $k = getenv($params['key']);
                return ($k === false) ? $default : $k;
            }, false )

            // Вызывается как: `{config key='url.public'}`, ключ key может быть опущен
            ->registerPlugin(Template::PLUGIN_FUNCTION, "config", static function($params)
            {
                return empty($params['key']) ? config() : config($params['key']);
            }, false)

            ->registerPlugin(Template::PLUGIN_MODIFIER, 'getenv', 'getenv', false)
            ->registerClass("Arris\AppRouter", "Arris\AppRouter")
            ;

        App::$template->setTemplate("_main.public.tpl");

        App::$flash = new FlashMessages();
    }

    public static function initDBConnection()
    {
        $app = self::factory();

        /**
         * Database
         */
        $db_credentials = [
            'driver'            =>  'mysql',
            'hostname'          =>  getenv('DB.HOST'),
            'database'          =>  getenv('DB.NAME'),
            'username'          =>  getenv('DB.USERNAME'),
            'password'          =>  getenv('DB.PASSWORD'),
            'port'              =>  getenv('DB.PORT'),
            'charset'           =>  getenv('DB.CHARSET'),
            'charset_collate'   =>  getenv('DB.COLLATE'),
            'slow_query_threshold'  => 1
        ];
        config('db_credentials', $db_credentials);

        App::$pdo = new DBWrapper(config('db_credentials'), [ 'slow_query_threshold' => 100 ], AppLogger::scope('mysql') );
        $app->addService('pdo', App::$pdo);
    }

    /**
     * @throws DatabaseError
     */
    public static function initAuth()
    {
        $app = self::factory();

        /**
         * Auth Delight
         */
        App::$auth = new Auth(new \PDO(
            sprintf(
                "mysql:dbname=%s;host=%s;charset=utf8mb4",
                config('db_credentials.database'),
                config('db_credentials.hostname')
            ),
            config('db_credentials.username'),
            config('db_credentials.password')
        ));
        $app->addService(Auth::class, App::$auth);
        config('auth', [
            'id'            =>  App::$auth->id(),
            'is_logged_in'  =>  App::$auth->isLoggedIn(),       // флаг "залогинен"
            'username'      =>  App::$auth->getUsername(),      // пользователь
            'email'         =>  App::$auth->getEmail(),
            'ipv4'          =>  \Arris\Helpers\Server::getIP(),                // IPv4
            'is_admin'      =>  App::$auth->hasRole(Role::ADMIN)
        ]);
    }

    public static function initMobileDetect()
    {
        $MOBILE_DETECT_INSTANCE = new \Detection\MobileDetect();
        config('features.is_mobile', [
            'cli'       =>  PHP_SAPI === "cli",
            'device'    =>  PHP_SAPI !== "cli" && $MOBILE_DETECT_INSTANCE->isMobile(),
            'domain'    =>  (bool)\preg_match("/^m\.(.*)/i", $_SERVER['HTTP_HOST'])
        ]);
    }

    public static function initErrorHandler()
    {
        // в DEV-режиме мы падаем на любой ошибке
        if (_env('ENV_STATE', 'prod') == 'dev') {
            ini_set("display_errors", "On");
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        }

        $errorHandler = new ErrorHandler();
        $errorHandler->setDebug(getenv('ENV_STATE') != 'prod');
        $errorHandler->register();

        $errorScreen = $errorHandler->getErrorScreen();
        if (!$errorHandler->isDebugEnabled() && $errorScreen instanceof WebErrorScreen) {
            $errorScreen->on(WebErrorScreenEvents::RENDER, static function ($event) {
                $event['heading'] = 'RPGClubs';
                $event['text'] = 'У нас что-то сломалось. Мы уже чиним.';
            });
        }

        if (getenv('ENV_STATE') == 'prod') {
            $errorScreen = $errorHandler->getErrorScreen();
            if (!$errorHandler->isDebugEnabled() && $errorScreen instanceof WebErrorScreen) {
                $errorScreen->on(WebErrorScreenEvents::RENDER, static function ($event) {
                    $event['heading'] = 'RPGClubs';
                    $event['text'] = 'У нас что-то сломалось. Мы уже чиним.';
                });
            }
        }

    }

    public static function initManticore()
    {
        return true;
    }

    /**
     * @throws \JsonException
     */
    public static function initRedis()
    {
        Cache::init([
            'enabled'   =>  getenv('REDIS.ENABLED'),
            'host'      =>  getenv('REDIS.HOST'),
            'port'      =>  getenv('REDIS.PORT'),
            'password'  =>  getenv('REDIS.PASSWORD'),
            'database'  =>  getenv('REDIS.DATABASE')
        ], [ ], App::$pdo);

        Cache::addRule('poi_types', [
            'source'    =>  CacheInterface::RULE_SOURCE_CALLBACK,
            'action'    =>  [ "\RPGCAtlas\Units\POITypes@getIcons" ],
            'ttl'       =>  CacheInterface::TIME_DAY
        ]);
    }

    public static function addCustomServices()
    {
        $app = self::factory();
        return true;
    }


}