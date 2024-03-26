<?php

namespace RPGCAtlas\Controllers;

use Arris\AppRouter;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;
use RPGCAtlas\TemplateHelper;

class AdminController extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
        $this->template->setTemplate("_admin.tpl");

        TemplateHelper::addInnerButton([
            'url'   =>  AppRouter::getRouter('view.admin.page.main'),
            'text'  =>  'Главная'
        ]);

        TemplateHelper::addInnerButton([
            'url'   =>  AppRouter::getRouter('view.admin.page.types'),
            'text'  =>  'Типы POI'
        ]);
    }

    public function view_admin_page_main()
    {
        // грузить список клубов сюда
        $this->template->assign("inner_template", 'admin/poi_list.tpl');
    }

    public function view_admin_page_types()
    {
        // список типов клубов
        $this->template->assign("inner_template", 'admin/poi_types.tpl');
    }


}