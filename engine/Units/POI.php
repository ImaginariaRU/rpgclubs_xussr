<?php

namespace RPGCAtlas\Units;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class POI extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * @return array
     */
    public function getPOIList()
    {
        $query = "SELECT * FROM {$this->tables->poi} WHERE `is_public` = 1 ORDER BY `is_public` DESC, `address_city`, `title` ";
        $dataset = [];

        foreach ($this->pdo->query($query)->fetchAll() as $row) {
            $data = $row;
            $data['coords'] = "{$row['lat']} / {$row['lng']}";
            $dataset[ $row['id'] ] = $data;
        }

        return $dataset;
    }

}