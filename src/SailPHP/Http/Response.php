<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 12:05 PM
 */

namespace SailPHP\Http;

use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    public function asPsr() : ResponseInterface
    {
        return static::toPsr($this);
    }

    public static function toPsr(SymfonyResponse $response) : ResponseInterface
    {
        $psrFactory = new DiactorosFactory();
        return $psrFactory->createResponse($response);
    }

    public static function fromPsr(ResponseInterface $response)
    {
        $httpFoundationFactory = new HttpFoundationFactory();
        return $httpFoundationFactory->createResponse($response);
    }

    public function json($data)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        $this->headers->set('Content-Type', 'application/json');
        $this->setContent($json);
        return $this->send();
    }

    public function redirect($url, $status = 302, $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }
}