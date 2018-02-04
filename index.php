<?php
/**
 * User: Arris
 * Date: 31.01.2018, time: 23:24
 */
define('__ROOT__', __DIR__);
define('PATH_CONFIG',   __ROOT__ . '/.config/');
define('PATH_FRONTEND', __ROOT__ . '/frontend/');
define('PATH_TEMPLATES',__ROOT__ . '/templates/');
define('PATH_STORAGE',  __ROOT__ . '/storage/');

require_once 'vendor/autoload.php';

use Pecee\SimpleRouter\SimpleRouter;

require_once 'engine/core.helpers.php';
require_once 'engine/core.routes.php';
require_once 'engine/core.functions.php';
require_once 'engine/websun.php';

$main_config = new \RPGCAtlas\Classes\INIConfig(PATH_CONFIG . 'config.ini');
$main_config->append(PATH_CONFIG . 'db.ini');
\RPGCAtlas\Classes\StaticConfig::set_config( $main_config );



// $dbi = \RPGCAtlas\Classes\DBConnectionStatic::getInstance();

// dd( $dbi->getConnection()->query("SHOW TABLES;")->fetchAll(\PDO::FETCH_COLUMN) );

// SimpleRouter::start();

