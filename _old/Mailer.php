<?php

namespace _old;

use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class Mailer extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function mailToAdmin($subject, $text)
    {
        $target = '';
    }

    public function mailToApplicant($email)
    {

    }

}