<?php

namespace RPGCAtlas\Controllers;

use Arris\Entity\Result;
use Arris\Template\TemplateInterface;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;
use RPGCAtlas\Units\GeoCoderDadata;
use RPGCAtlas\Units\GeoCoderNominatim;
use RPGCAtlas\Units\POI;

class AjaxController extends AbstractClass
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
        $poi = (new POI())->getPOIItem($id);

        $poi['title'] = htmlspecialchars($poi['title'], ENT_QUOTES | ENT_HTML5);

        $this->template->assign('dataset', $poi);
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

    public function get_coords_by_address()
    {
        $address = $_REQUEST['poi_address'];

        $r = (new GeoCoderDadata())->getCoordsByAddress($address);

        $this->template->setRenderType(TemplateInterface::CONTENT_TYPE_JSON_RAW);
        $this->template->setJSON($r);
    }

}