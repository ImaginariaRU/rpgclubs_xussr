<?php

namespace RPGCAtlas\Units;

use Arris\Database\DBWrapper;
use Arris\Entity\Result;
use Arris\Helpers\Server;
use RPGCAtlas\AbstractClass;

class VisitLogger extends AbstractClass
{
    const QUERY_TABLE_DEFINITION = <<<QUERY_TABLE_DEFINE
CREATE TABLE IF NOT EXISTS `%s` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `dayvisit` date DEFAULT NULL COMMENT 'дата',
    `ipv4` int(10) unsigned DEFAULT NULL COMMENT 'ipv4 long',
    `hits` int(11) DEFAULT NULL COMMENT 'hits с айпишника в этот день',
    `id_poi` int(11) DEFAULT 0 COMMENT 'POI показа, 0 для главной страницы',
    PRIMARY KEY (`id`),
    UNIQUE KEY `date+ipv4` (`dayvisit`,`ipv4`),
    KEY `ipv4` (`ipv4`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;


QUERY_TABLE_DEFINE;

    const QUERY_CHECK_TABLE_EXIST = <<<QUERY_CHECK_TABLE_EXIST
SHOW TABLES LIKE '%s';
QUERY_CHECK_TABLE_EXIST;

    const QUERY_INSERT = <<<QUERY_INSERT
INSERT INTO `%s` (dayvisit, ipv4, hits, id_poi) VALUES(CURDATE(), INET_ATON(:ipv4), 1, :id_poi)
ON DUPLICATE KEY UPDATE hits = hits+1
QUERY_INSERT;

    public static function log(DBWrapper $pdo, $table_name = 'visitlog', $id_poi = 0): Result
    {
        $result = new Result();

        try {
            $query = sprintf(self::QUERY_CHECK_TABLE_EXIST, $table_name);
            if (!$pdo->query($query)->rowCount()) {
                $query = sprintf(self::QUERY_TABLE_DEFINITION, $table_name);

                $state = $pdo->query($query);
            }

            $query = sprintf(self::QUERY_INSERT, $table_name);
            $sth = $pdo->prepare($query);

            $sth->execute( array(
                'id_poi'        =>  $id_poi,
                'ipv4'          =>  Server::getIP(),
            ) );

        } catch (\PDOException $e) {
            $result->error($e->getMessage());
        }

        return $result;
    }

}