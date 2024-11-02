<?php

namespace RPGCAtlas\Controllers;

use Psr\Log\LoggerInterface;
use RPGCAtlas\Units\POI;

class AjaxController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * @param $id
     * @return void
     */
    public function view_poi_page($id)
    {
        $poi = (new POI())->getItem($id);

        $poi['title'] = htmlspecialchars($poi['title'], ENT_QUOTES | ENT_HTML5);

        $this->template->assign('dataset', $poi);
        $this->template->setTemplate("public/ajax_poi_info.tpl");
    }

    public function ajax_view_poi_list()
    {
        $dataset = (new POI())->getList();

        $this->template->assign('dataset', $dataset);
        $this->template->assign('summary', [
            'clubs_total'   =>  count($dataset),
            'clubs_visible' =>  count(array_filter($dataset, function($data){ return !!$data['is_public']; }) )
        ]);

        $this->template->setTemplate("public/ajax_poi_list.tpl");
    }



}