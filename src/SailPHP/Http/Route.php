<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 12:27 PM
 */

namespace SailPHP\Http;

use \RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SailPHP\Controller\Controller;
use Relay\RelayBuilder;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
class Route
{
    private $controller;

    private $path;

    private $method;

    private $middleware;

    private $match = [];

    public function __construct($match)
    {
        $this->match = $match;

        $parts = explode('@', $this->match['controller']);

        if(!is_array($parts) || count($parts) != 2) {
            app()->notFound();
            die();
        }

        list($this->controller, $this->method) = $parts;
        $this->controller = "App\\Controllers\\".$this->controller;

        if(!class_exists($this->controller)) {
            throw new \RuntimeException("Controller not found: ".$this->controller);
        }
    }

    public function __destruct()
    {
        $controller = $this->controller;
        $method = $this->method;

        if(!class_exists($controller)) {
            return;
        }

        $controller = new $controller();
        if(!method_exists($controller, $method)) {
            throw new \RuntimeException("Method not found.");
        }

        unset($this->match['controller'], $this->match['_route']);

        $params = $this->match;
        return call_user_func_array([$controller, $method], $params);
    }

    public function match()
    {
        // $controller = $this->controller;
        // $method = $this->method;

        // $controller = new $controller();
        // if(!method_exists($controller, $method)) {
        //     throw new \RuntimeException("Method not found.");
        // }

        // unset($this->match['controller'], $this->match['_route']);

        // $params = $this->match;
        // return call_user_func_array([$controller, $method], $params);
    }

    public static function any($path, $name, $controller)
    {
        $options = [
            'name'  => $name,
            'controller'    => $controller
        ];

        $route = route()->route('*', $path, $options);
        return $route;
    }

    public static function get($path, $name, $controller)
    {
        $options = [
            'name'  => $name,
            'controller'    => $controller
        ];

        $route = route()->route('GET', $path, $options);
        return $route;
    }

    public static function post($path, $name, $controller)
    {
        $options = [
            'name'  => $name,
            'controller'    => $controller
        ];

        $route = route()->route('POST', $path, $options);
        return $route;
    }

    public static function group(array $attributes, \Closure $callback)
    {
        route()->group($attributes, $callback);
    }


}
