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

function viewReturn($name, $parameters = array())
{
    return container('template')->renderReturn($name, $parameters);
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

function redirect($url, $status = 200)
{
    if($status != 200) {
        header("Location: ".$url, true, $status);
    } else {
        header("Location: ".$url);
    }
    return;
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

function session()
{
    return container('session');
}

function auth()
{
    return container('auth');
}

function loggedIn()
{
    return auth()->loggedIn();
}

function user()
{
    if(!loggedIn()) {
        return null;
    }

    return auth()->user();
}

function public_path()
{
    return paths('public').'/';
}

function asset_path()
{
    return public_path().'assets/';
}

function links($paginate) {
    $presenter = new SailPHP\Database\Presenter($paginate);
    echo $presenter->links();
    return;
}

function setCurrentPage($page = null) {
    if (is_null($page)){
        if (input('page') !== null && input('page') !== ''){
            $page = input('page');
        } else {
            $page = 1;
        }
    }

    Illuminate\Pagination\Paginator::currentPageResolver(function() use ($page) {
        return $page;
    });
}