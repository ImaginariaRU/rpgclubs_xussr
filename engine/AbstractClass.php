<?php

namespace RPGCAtlas;

use Arris\Database\DBWrapper;
use Arris\Template\Template;
use Psr\Log\LoggerInterface;

#[AllowDynamicProperties]
class AbstractClass
{
    public ?\Arris\App $app;

    public DBWrapper $pdo;

    public Template $template;

    public array $options = [];

    public $tables;

    public function __construct($options = [], LoggerInterface $logger = null)
    {
        $this->app = \RPGCAtlas\App::factory();

        $this->pdo = App::$pdo;
        $this->template = App::$template;

        $this->options = $options;

        $this->tables = new DBConfigTables();
    }

}