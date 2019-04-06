<?php

namespace SailPHP\Exception;

use Throwable;

class NoAuthableLoggedInException extends \Exception
{
    public function __construct()
    {
        parent::__construct("No active login.");
    }
}