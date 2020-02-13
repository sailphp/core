<?php

namespace SailPHP\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Class Cookie
 * @package SailPHP\Http
 */
class Cookie
{
    /**
     * @var
     */
    protected $response;
    /**
     * @var
     */
    protected $response_headers;

    /**
     * @param SymfonyResponse $response
     */
    public function setResponse(SymfonyResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @param $method
     * @param $arguements
     * @return bool|mixed
     */
    public function __call($method, $arguements)
    {
        if(!$this->response) {
            return false;
        }

        $expected_method = $method . 'Cookie' . ($method == 'get' ? 's' : '');

        if(method_exists($this->response->headers, $expected_method)) {
            return call_user_func_array(array($this->response_headers, $expected_method), $arguements);
        }

        return false;
    }
}
