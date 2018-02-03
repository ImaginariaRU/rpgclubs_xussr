<?php
/**
 * User: Arris
 *
 * Class DBConnectionLite
 * Namespace: RPGCAtlas\Classes
 *
 * Date: 03.02.2018, time: 20:52
 */

namespace RPGCAtlas\Classes;

/**
 *
 * Class DBConnectionLite v 1.2
 * @package RPGCAtlas\Classes
 */
class DBConnectionLite
{
    private $database_settings = array();

    /**
     * @var \PDO $pdo_connection
     */
    private $pdo_connection;
    private $table_prefix = '';
    public $is_connected = FALSE;

    /**
     *
     * @param $config
     */
    public function __construct($config):DBConnectionLite
    {
        if (is_array($config)) {
            // Если конфиг передан как массив параметров (содержимое определенной секции)
            $database_settings = $config;
            $this->table_prefix = $config['table_prefix'] ?? '';
        }
        elseif (get_class($config) === 'INIConfig') {

            /**
             * @var INIConfig $config
             */
            $section_name = $config->get( "connection/suffix" );
            $database_settings = $config->get( "database:$section_name");
            $this->table_prefix = $config->get( "database:$section_name/table_prefix");

        } else {
            $message = "Unknown config";

            //@todo: MONOLOG

            die($message);
        }

        $this->database_settings = $database_settings;

        $dbhost = $database_settings['hostname'];
        $dbname = $database_settings['database'];
        $dbuser = $database_settings['username'];
        $dbpass = $database_settings['password'];
        $dbport = $database_settings['port'];

        $dsl = "mysql:host=$dbhost;port=$dbport;dbname=$dbname";

        try {
            $dbh = new \PDO($dsl, $dbuser, $dbpass);

            $dbh->exec("SET NAMES utf8 COLLATE utf8_unicode_ci");
            $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            $this->pdo_connection = $dbh;
        } catch (\PDOException $e) {
            $message = "Unable to connect `{$dsl}`, PDO CONNECTION ERROR: " . $e->getMessage() . "\r\n";

            //@todo: MONOLOG

            $this->connect_error = "Database connection error!: " . $e->getMessage() . "<br/>";
            $this->pdo_connection = null;
            return false;
        }
        $this->is_connected = true;
        return true;
    }

    /**
     * @return \PDO
     */
    public function getconnection():\PDO
    {
        return $this->pdo_connection;
    }


}

/* end class.DBConnectionLite.php */