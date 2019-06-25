<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 2:14 PM
 */

namespace SailPHP\Html;


use RuntimeException;
use SailPHP\Foundation\App;

class Template
{
    private $loader = null;

    private $twig = null;

    private $app = null;
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->loader = new \Twig\Loader\FilesystemLoader(paths('base').'/templates');
        $this->twig = new \Twig\Environment($this->loader);

        $this->addFunctions();
    }

    private function addFunctions()
    {
        // Route
        if($this->app->has('router')) {
            $function = new \Twig\TwigFunction('route', function() {
                return $this->app->get('router');
            });
            $this->twig->addFunction($function);

            // Url
            $function = new \Twig\TwigFunction('url', function($url, $options = array()) {
                return $this->app->get('router')->path($url, $options);
            });
            $this->twig->addFunction($function);
        }

        // Config
        if($this->app->has('config')) {
            $function = new \Twig\TwigFunction('config', function() {
                return $this->app->get('config');
            });
            $this->twig->addFunction($function);
        }

        // User
        if($this->app->has('auth')) {
            $function = new \Twig\TwigFunction('user', function($var) {
                if($this->app->get('auth')->loggedIn()) {
                    return user()->$var;
                }
                return null;
            });
            $this->twig->addFunction($function);
        }
    }

    public function addGlobal($name, $data)
    {
        $this->twig->addGlobal($name, $data);
    }

    public function addFunction($name, $func) {
        $function = new \Twig\TwigFunction($name, $func);

        $this->twig->addFunction($function);
    }
    
    public function render($file, $parameters = array())
    {
        echo $this->twig->render($file.".html", $parameters);
    }
}