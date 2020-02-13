<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 11:33 AM
 */

namespace SailPHP\Http;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest
{
    /**
     * Construct our parent with all of the REQUEST
     * Super globals.
     */
    public function __construct()
    {
        parent::__construct(
            $_GET,
            $_POST,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    /**
     * @return RequestInterface
     */
    public function asPsr() : RequestInterface
    {
        return static::toPsr($this);
    }

    /**
     * @param SymfonyRequest $request
     * @return RequestInterface
     */
    public static function toPsr(SymfonyRequest $request) : RequestInterface
    {
        $psrFactory = new DiactorosFactory();
        return $psrFactory->createRequest($request);
    }
}
