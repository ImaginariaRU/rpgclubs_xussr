<?php
/**
 * User: Arris
 *
 * Class VisitLogger
 * Namespace: RPGCAtlas\Classes
 *
 * Date: 14.02.2018, time: 2:16
 */

namespace RPGCAtlas\Classes;

class VisitLogger
{
    const QUERY_TABLE_DEFINITION = <<<QUERY_TABLE_DEFINE
CREATE TABLE IF NOT EXISTS `%s` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_banner` INT(11) DEFAULT NULL,
  `alias_banner` CHAR(8) DEFAULT NULL,
  `dayvisit` DATE DEFAULT NULL,
  `ipv4` INT(10) UNSIGNED DEFAULT NULL,
  `hits` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date+ipv4` (`dayvisit`,`ipv4`),
  KEY `ipv4` (`ipv4`),
  KEY `id_banner` (`id_banner`)
) ENGINE=MYISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
QUERY_TABLE_DEFINE;

    const QUERY_CHECK_TABLE_EXIST = <<<QUERY_CHECK_TABLE_EXIST
SHOW TABLES LIKE '%s';
QUERY_CHECK_TABLE_EXIST;

    const QUERY_INSERT = <<<QUERY_INSERT
INSERT INTO `%s` (alias_banner, dayvisit, ipv4, hits) VALUES(:alias_banner, CURDATE(), INET_ATON(:ipv4), 1)
ON DUPLICATE KEY UPDATE hits = hits+1
QUERY_INSERT;

    private static function getIP()
    {
        if (!isset ($_SERVER['REMOTE_ADDR'])) {
            return NULL;
        }

        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
            $http_x_forwared_for = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
            $client_ip = trim(end($http_x_forwared_for));
            if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
                return $client_ip;
            }
        }

        return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : NULL;
    }

    public static function log(\PDO $_pdo, $table_name, $table_key = NULL)
    {
        $insert_state = TRUE;
        if (! ($_pdo instanceof \PDO)) return NULL;

        $query = sprintf(self::QUERY_CHECK_TABLE_EXIST, $table_name);
        if (!$_pdo->query($query)->rowCount()) {
            $query = sprintf(self::QUERY_TABLE_DEFINITION, $table_name);

            $state = $_pdo->query($query);
        }

        $query = sprintf(self::QUERY_INSERT, $table_name);
        $sth = $_pdo->prepare($query);

        try {
            $sth->execute( array(
                'ipv4'          =>  self::getIP(),
                'alias_banner'  =>  substr($table_key, 0, 8)
            ) );
        } catch (\PDOException $e) {
            $insert_state = $e->getCode();
        }

        return $insert_state;
    }

}