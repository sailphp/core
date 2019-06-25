<?php

namespace SailPHP\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Cookie
{
    protected $response;

    public function setResponse(SymfonyResponse $response)
    {
        $this->response = $response;
    }

    public function __call($method, $arguements)
    {
        if(!$this->response) {
            return false;
        }

        $expected_method = $method . 'Cookie' . ($method == 'get' ? 's' : '');

        if(method_exists($this->response->headers, $expected_method)) {
            return call_user_method_array($expected_method, $this->response_headers, $arguements);
        }

        return false;
    }
}