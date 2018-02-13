<?php
/**
 * User: Arris
 * Date: 31.01.2018, time: 23:24
 */
define('__ROOT__', __DIR__);
define('__CONFIG__', __ROOT__ . '/.config/');
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

$config = new \RPGCAtlas\Classes\INIConfig(PATH_CONFIG . 'config.ini');
$config->append(PATH_CONFIG . 'db.ini');

\RPGCAtlas\Classes\StaticConfig::set_config( $config );
\RPGCAtlas\Classes\StaticConfig::set('copyright/title', '0.2.16 "Haskuldr"');

\RPGCAtlas\Classes\VisitLogger::log(\RPGCAtlas\Classes\DBStatic::getConnection(), 'rpgcrf_clubs_visitlog', 'rpgcrfip');

SimpleRouter::start();

