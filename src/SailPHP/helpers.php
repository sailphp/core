<?php

use SailPHP\Foundation\Container;
use Respect\Validation\Validator;

function container($make = null)
{
    if(is_null($make)) {
        return Container::getInstance();
    }

    return Container::getInstance()->get($make);
}

function app()
{
    return container('app');
}

function response()
{
    return container('response');
}

function request()
{
    return container('request');
}

function route()
{
    return container('router');
}

function config()
{
    return container('config');
}

function url($name, $requirements = [])
{
    return route()->path($name, $requirements);
}

function addViewGlobal($name, $value)
{
    return container('template')->addGlobal($name, $value);
}

function view($name, $parameters = array())
{
    return container('template')->render($name, $parameters);
}

function paths($key = false)
{
    $paths = app()->getPaths();
    if ($key && isset($paths[$key])) {
        return rtrim($paths[$key], '/');
    }
    return $paths;
}

function validate()
{
    return Validator::class;
}

function redirect($url)
{
    return response()->redirect($url);
}

function input($name, $clean = true)
{
    $input = request()->get($name);
    if($clean) {
        $input = clean($input);
    }

    return $input;
}

function clean($key)
{
    return trim(addslashes(htmlentities($key)));
}