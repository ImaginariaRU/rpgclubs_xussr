<?php

namespace RPGCAtlas\Controllers;

use Psr\Log\LoggerInterface;

class AdminController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function view_admin_page_main()
    {
        // кол-во POI
        // тикетов
        // типов иконок
        // посещений сегодня/всего
        // посещений клубов топ сегодня/всего

        $this->template->setTemplate('_admin.tpl');
    }

}