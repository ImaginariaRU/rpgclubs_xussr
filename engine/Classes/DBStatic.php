<?php
/**
 * User: Arris
 *
 * Class DBConnectionStatic
 * Namespace: Classes
 *
 * Date: 04.02.2018, time: 16:09
 */

namespace RPGCAtlas\Classes;

use RPGCAtlas\Classes\StaticConfig;

final class DBStatic
{
    /**
     * @var DBStatic $_instance
     */
    private static $_instance;

    /**
     * @var object $_config
     */
    private static $_config;

    public static $_table_prefix = '';

    private static $is_connected = FALSE;

    /**
     * @var \PDO $pdo_connection
     */
    private static $pdo_connection;

    private static $connection_error;


    /* ================================================================== */


    private function get_pdo():\PDO
    {
        return self::$pdo_connection;
    }

    public static function getInstance():DBStatic
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }


    public static function getConnection():\PDO
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance->get_pdo();
    }

    private function __construct()
    {
        $section_name = StaticConfig::get("connection/suffix");
        self::$_config = StaticConfig::get( "database:$section_name");
        self::$_table_prefix = StaticConfig::get( "database:$section_name/table_prefix");

        $dbhost = self::$_config['hostname'];
        $dbname = self::$_config['database'];
        $dbuser = self::$_config['username'];
        $dbpass = self::$_config['password'];
        $dbport = self::$_config['port'];

        $dsl = "mysql:host=$dbhost;port=$dbport;dbname=$dbname";

        try {
            $dbh = new \PDO($dsl, $dbuser, $dbpass);

            $dbh->exec("SET NAMES utf8 COLLATE utf8_unicode_ci");
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            self::$pdo_connection = $dbh;
        } catch (\PDOException $e) {
            $message = "Unable to connect `{$dsl}`, PDO CONNECTION ERROR: " . $e->getMessage() . "\r\n";

            //@todo: MONOLOG

            self::$connection_error = "Database connection error!: " . $e->getMessage() . "<br/>";
            self::$pdo_connection = null;
            return false;
        }
        self::$is_connected = true;
        return true;
    }

}