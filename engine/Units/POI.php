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
     * @param bool $is_logged_in
     *
     * @return array
     */
    public function getList(bool $is_logged_in = false): array
    {
        $sub_query_public   = $is_logged_in ? "AND is_public = 1" : "";
        $sub_query_order    = $is_logged_in ? "is_public DESC, dt_create DESC" : "address_city, title";

        $query = "
SELECT * 
FROM {$this->tables->poi} 
WHERE 
      lat IS NOT NULL 
  AND lng IS NOT NULL
  {$sub_query_public}
ORDER BY {$sub_query_order} 
";

        $dataset = [];

        foreach ($this->pdo->query($query)->fetchAll() as &$row) {
            $row['coords'] = "{$row['lat']} / {$row['lng']}";
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