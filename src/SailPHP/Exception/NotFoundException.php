<?php


namespace SailPHP\Exception;

class NotFoundException extends \Exception
{
    public function __construct($path)
    {
        header("HTTP/1.0 404 Not Found");
    }
}