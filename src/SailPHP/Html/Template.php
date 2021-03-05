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

    private $extension = 'html';

    public function __construct(App $app, $extension = 'html')
    {
        $this->app = $app;
        $this->loader = new \Twig\Loader\FilesystemLoader(paths('base').'/templates');
        $this->twig = new \Twig\Environment($this->loader);
        $this->extension = $extension;
        $this->addFunctions();
    }

    public function getExtension()
    {
        return $this->extension;
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

        // Pagination Links
        $function = new \Twig\TwigFunction('links', function($var) {
            return links($var);
        });
        $this->twig->addFunction($function);

        $template = $this;
        if(file_exists($this->app->getPaths()['app'] . '/template.php')) {
            require_once($this->app->getPaths()['app'].'/template.php');
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
        echo $this->twig->render($file.".".$this->extension, $parameters);
        
        return;
    }

    public function renderReturn($file, $parameters = array())
    {
        return $this->twig->render($file.".".$this->extension, $parameters);;
    }
}
