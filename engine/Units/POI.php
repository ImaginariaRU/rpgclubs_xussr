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
     * Возвращает массив точек интереса
     *
     * @return array
     */
    public function getList()
    {
        $query = "
SELECT * 
FROM {$this->tables->poi} 
WHERE is_public = 1 
  AND lat IS NOT NULL 
  AND lng IS NOT NULL
ORDER BY is_public DESC, `address_city`, `title` 
";
        $dataset = [];

        foreach ($this->pdo->query($query)->fetchAll() as &$row) {
            $row['coords'] = "{$row['lat']} / {$row['lng']}";

            // еще нужно определить реального владельца по айди (id_owner)
            //@todo: реальный владелец (для админа показывает логин владельца, для владельца - "Я"

            $dataset[ $row['id'] ] = $row;
        }

        return $dataset;
    }

    /**
     * Возвращает данные по точке интереса
     *
     * @param $id
     * @return array
     */
    public function getItem($id)
    {
        $query = "SELECT * FROM {$this->tables->poi} WHERE id = :id ORDER BY id DESC LIMIT 1";

        $sth = $this->pdo->prepare($query);
        $sth->execute([ 'id' => $id ]);

        return $sth->fetch() ?: [];
    }

}