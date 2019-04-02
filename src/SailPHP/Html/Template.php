<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 2:14 PM
 */

namespace SailPHP\Html;


use RuntimeException;

class Template
{
    private $loader = null;

    private $twig = null;

    public function __construct()
    {
        $this->loader = new \Twig\Loader\FilesystemLoader(paths('base').'/templates');
        $this->twig = new \Twig\Environment($this->loader);

        $this->addFunctions();
    }

    private function addFunctions()
    {
        // Route
        $function = new \Twig\TwigFunction('route', function() {
            return route();
        });
        $this->twig->addFunction($function);

        // Url
        $function = new \Twig\TwigFunction('url', function($url, $options = array()) {
            return url($url, $options);
        });
        $this->twig->addFunction($function);

        // Config
        $function = new \Twig\TwigFunction('config', function() {
            return config();
        });
        $this->twig->addFunction($function);

    }

    public function addGlobal($name, $data)
    {
        $this->twig->addGlobal($name, $data);
    }
    
    public function render($file, $parameters = array())
    {
        echo $this->twig->render($file.".html", $parameters);
    }
}