<?php
/**
 * User: Arris
 * Date: 31.01.2018, time: 23:24
 */

require_once 'vendor/autoload.php';

use Pecee\SimpleRouter\SimpleRouter;

require_once 'engine/core.helpers.php';
require_once 'engine/core.routes.php';
require_once 'engine/websun.php';

// $monologger = new \Monolog\Logger( ('RPGClubAtlas') );

SimpleRouter::start();

