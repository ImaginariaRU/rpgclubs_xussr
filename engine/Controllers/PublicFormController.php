<?php

namespace RPGCAtlas\Controllers;

use Psr\Log\LoggerInterface;

class PublicFormController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * Рисует форму добавления нового клуба
     *
     * @return void
     */
    public function view_form_poi_add()
    {
        $this->template->setTemplate('public/form_add_club.tpl');
    }

}