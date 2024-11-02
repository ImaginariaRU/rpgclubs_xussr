<?php

namespace RPGCAtlas\Controllers;

use Psr\Log\LoggerInterface;
use RPGCAtlas\Units\POI;

/**
 * Контроллер про работу с POI как от лица админа, так и нет
 *
 * От лица админа нет капчи и есть больше функций
 * От лица анона есть капча, нет определений координат и
 */
class PlacesController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function viewList()
    {
        $dataset = (new POI())->getList();

        $this->template->assign('dataset', $dataset);

        $this->template->setTemplate("places/list.tpl");
    }

    public function formAdd()
    {
        $this->template->setTemplate('places/form_add_poi.tpl');
    }

}