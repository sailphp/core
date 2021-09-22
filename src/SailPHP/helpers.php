<?php

use SailPHP\Auth\Authable;
use SailPHP\Foundation\Container;
use Respect\Validation\Validator;

/**
 * Get available container instance.
 *
 * @param null $make
 * @return mixed|\SailPHP\Foundation\Container;
 */
function container($make = null)
{
    if(is_null($make)) {
        return Container::getInstance();
    }

    return Container::getInstance()->get($make);
}

/**
 * Get available container app instance.
 *
 * @return mixed|\SailPHP\Foundation\App;
 */
function app()
{
    return container('app');
}

/**
 * Return a new response from the application.
 *
 * @return mixed|\SailPHP\Http\Response;
 */
function response()
{
    return container('_response');
}

/**
 * Return instance of current request
 *
 * @return mixed|\SailPHP\Http\Request;
 */
function request()
{
    return container('request');
}

function csrf() 
{
    return container('csrf');
}

/**
 * Return instance of Router.
 *
 * @return mixed|\SailPHP\Http\Router;
 */
function route()
{
    return container('router');
}

/**
 * Get the specified configuration value.
 *
 * @return mixed|\SailPHP\Foundation\Config;
 */
function config()
{
    return container('config');
}

/**
 * Generate the URL from a named route
 *
 * @param $name
 * @param array $requirements
 * @return string
 */
function url($name, $requirements = [])
{
    return route()->path($name, $requirements);
}


function addViewGlobal($name, $value)
{
    return container('template')->addGlobal($name, $value);
}

/**
 * Return template
 *
 * @param $name
 * @param array $parameters
 * @return mixed|string
 */
function view($name, $parameters = array())
{
    return container('template')->render($name, $parameters);
}

function viewReturn($name, $parameters = array())
{
    return container('template')->renderReturn($name, $parameters);
}

/**
 * Return paths for key
 *
 * @param bool $key
 * @return array|string
 */
function paths($key = false)
{
    $paths = app()->getPaths();
    if ($key && isset($paths[$key])) {
        return rtrim($paths[$key], '/');
    }
    return $paths;
}

/**
 * Validator Class
 *
 * @return \Respect\Validation\Validator;
 */
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
    return trim(addslashes(htmlspecialchars($key, ENT_QUOTES, 'UTF-8', false)));
}

/**
 * @return \SailPHP\Session\Session
 */
function session()
{
    return container('session');
}


/**
 * @return \SailPHP\Auth\SessionAuthAdapter
 */
function auth()
{
    return container('auth');
}

function loggedIn()
{
    return auth()->loggedIn();
}

/**
 * @return null|\SailPHP\Auth\Authable
 * @throws \SailPHP\Exception\NoAuthableLoggedInException
 */
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
