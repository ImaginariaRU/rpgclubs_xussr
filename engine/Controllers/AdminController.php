<?php

namespace RPGCAtlas\Controllers;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class AdminController extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

}