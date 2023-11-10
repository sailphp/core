<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 12:27 PM
 */

namespace SailPHP\Http;

class Route
{
    private $controller;

    private $path;

    private $method;

    private $middleware;

    private $match = [];

    public function __construct($match)
    {
        if(is_null($match)) {
            app()->notFound();
            die();
        }

        $this->match = $match;

        $old = false;
        if (is_array($match['controller'])) {
            $parts = $match['controller'];  
        } else {
            $old = true;
            $parts = explode('@', $this->match['controller']);
        }
        
        if(!is_array($parts) || count($parts) != 2) {
            app()->notFound();
            die();
        }

        list($this->controller, $this->method) = $parts;
        if ($old) {
            $this->controller = "App\\Controllers\\".$this->controller;
        }

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
        return self::addMethod($path, $name, $controller, 'GET');
    }

    public static function post($path, $name, $controller)
    {
        return self::addMethod($path, $name, $controller, 'POST');
    }

    public static function delete($path, $name, $controller)
    {
        return self::addMethod($path, $name, $controller, 'DELETE');
    }

    public static function patch($path, $name, $controller)
    {
        return self::addMethod($path, $name, $controller, 'PATCH');
    }

    public static function put($path, $name, $controller)
    {
        return self::addMethod($path, $name, $controller, 'PUT');
    }

    public static function head($path, $name, $controller)
    {
        return self::addMethod($path, $name, $controller, 'HEAD');
    }

    public static function options($path, $name, $controller)
    {
        return self::addMethod($path, $name, $controller, 'OPTIONS');
    }


    private static function addMethod($path, $name, $controller, $method)
    {
        $options = [
            'name'  => $name,
            'controller'    => $controller
        ];

        $route = route()->route($method, $path, $options);
        return $route;
    }

    public static function group(array $attributes, \Closure $callback)
    {
        route()->group($attributes, $callback);
    }
}
