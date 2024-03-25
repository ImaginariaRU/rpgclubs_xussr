<?php

namespace RPGCAtlas\Controllers;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;
use RPGCAtlas\Units\POI;

class AjaxController extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function view_poi_page($id)
    {
        $query = "SELECT * FROM {$this->tables->poi} WHERE id = :id ORDER BY id DESC LIMIT 1";

        $sth = $this->pdo->prepare($query);
        $sth->execute([ 'id' => $id ]);

        $club = $sth->fetch();
        $club['title'] = htmlspecialchars($club['title'], ENT_QUOTES | ENT_HTML5);

        $this->template->assign('dataset', $club);
        $this->template->setTemplate("public/ajax_poi_info.tpl");
    }

    public function ajax_view_poi_list()
    {
        $dataset = (new POI())->getPOIList();

        $this->template->assign('dataset', $dataset);
        $this->template->assign('summary', [
            'clubs_total'   =>  count($dataset),
            'clubs_visible' =>  count(array_filter($dataset, function($data){ return !!$data['is_public']; }) )
        ]);

        $this->template->setTemplate("public/ajax_poi_list.tpl");
    }

}