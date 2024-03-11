<?php

namespace RPGCAtlas\Units;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class Map extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function getClubs()
    {
        $query = "
        SELECT `id`, `lat`, `lng`
FROM {$this->tables->clubs}
WHERE `is_public` = 1 
  AND `lat` IS NOT NULL 
  AND `lng` IS NOT NULL
ORDER BY `id`
        ";
        $dataset = [];

        foreach ($this->pdo->query($query)->fetchAll() as &$row) {
            /*if (empty($row['lat']) || empty($row['lng']) || $row['lat'] == 0.0000000 || $row['lng'] == 0.0000000) {
                continue;
            }*/

            $row['coords'] = "{$row['lat']} / {$row['lng']}";

            // еще нужно определить реального владельца по айди (id_owner)
            //@todo: реальный владелец (для админа показывает логин владельца, для владельца - "Я"

            $dataset[ $row['id'] ] = $row;
        }

        return $dataset;
    }

}