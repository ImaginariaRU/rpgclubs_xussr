<?php

namespace RPGCAtlas\Exceptions;

use Arris\AppRouter;
use Throwable;

class AccessDeniedException extends \RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . '<br><br><a href="'. AppRouter::getRouter('view.main.page') .'"> На главную страницу </a> <br>', $code, $previous);
    }

}