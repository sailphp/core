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

/**
 * Class Response
 * @package SailPHP\Http
 */
class Response extends SymfonyResponse
{
    /**
     * @return ResponseInterface
     */
    public function asPsr() : ResponseInterface
    {
        return static::toPsr($this);
    }

    /**
     * @param SymfonyResponse $response
     * @return ResponseInterface
     */
    public static function toPsr(SymfonyResponse $response) : ResponseInterface
    {
        $psrFactory = new DiactorosFactory();
        return $psrFactory->createResponse($response);
    }

    /**
     * @param ResponseInterface $response
     * @return SymfonyResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public static function fromPsr(ResponseInterface $response)
    {
        $httpFoundationFactory = new HttpFoundationFactory();
        return $httpFoundationFactory->createResponse($response);
    }

    /**
     * @param $data
     * @return Response
     */
    public function json($data, $code = 200)
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        $this->headers->set('Content-Type', 'application/json');
        $this->setContent($json);
        $this->setStatusCode($code);
        return $this->send();
    }

    /**
     * @param $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302, $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }
}
