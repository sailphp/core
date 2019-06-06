<?php

namespace SailPHP\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Middleware
{
    public function __invoke($params = array())
    {
        return $this->handle($params = array());
    }

    public abstract function handle($params = array());
}