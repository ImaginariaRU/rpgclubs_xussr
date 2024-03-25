<?php

namespace RPGCAtlas\Units;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class POITypes extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * @param null $id
     * @return array[id, `type`, icon, marker_color, marker_offset_x, marker_offset_y]
     */
    public function getIcons($id = null)
    {
        $query = "SELECT id, `type`, icon, marker_color, marker_offset_x, marker_offset_y FROM {$this->tables->poi_types}";
        $dataset = [];

        if (!is_null($id)) {
            $dataset['id'] = $id;
            $query .= " WHERE id = :id ";
        }
        $sth = $this->pdo->prepare($query);
        $sth->execute($dataset);

        return $sth->fetch() ?: [];
    }

}