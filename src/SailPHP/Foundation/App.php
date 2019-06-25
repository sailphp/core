<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 10:59 AM
 */

namespace SailPHP\Foundation;

use Dotenv\Dotenv;
use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use SailPHP\Http\Route;
use SailPHP\Html\Template;
class App
{
    public $container;

    private $listeners = [];

    private $paths = [];

    private $middlewares = [];

    public function __construct(array $paths, array $providers = [])
    {
        $this->setupInitialPaths($paths);

        $this->container = container();
        $this->container->set('app', $this);

        foreach($providers as $name => $provider) {
            $this->container->bind($name, $provider);
        }

        $this->container->get('config')->loadConfigurationFiles(
            $this->paths['base'].'/config',
            $this->getEnv()
        );

        $this->database();
        // If Session is injected into container, lets call session start
        if($this->container->has('session')) {
            $this->container->get('session')->start();
        }
        
        if($this->container->has('cookie')) {
            $this->container->get('cookie')->setResponse(
                $this->container->get('response')
            );
        }
    }

    public function has($name)
    {
        return $this->container->has($name);
    }

    public function get($name)
    {
        return $this->container->get($name);
    }


    public function getPaths()
    {
        return $this->paths;
    }

    public function middlewares()
    {
        return $this->middlewares;
    }

    public function notFound()
    {
        header("HTTP/1.0 404 Not Found");
        try {
            echo view('errors/404', array());
        } catch(\Throwable $e) {
            header("HTTP/1.0 404 Not Found");
            echo '<h1>404 Not Found</h1>';
        }
        
        die();
    }

    private function setupInitialPaths(array $paths)
    {
        if (! isset($paths['base'], $paths['app'], $paths['public'], $paths['storage'])) {
            throw new InvalidArgumentException(
                'Paths requires keys base, app, public and storage'
            );
        }

        $this->paths = $paths;
        $this->paths['env_file'] = $this->paths['base'].'/.env';
    }

    public function listen()
    {
        $this->loadMiddlewares();
        $this->loadRouteFiles();

        $match = $this->container->get('router')->match($this->container->get('request'));
        $route = new Route($match);

        $this->container->bind('response', $route->match());
    }

    public function render()
    {
        $response = $this->container->get('response');
        $response->prepare($this->container->get('request'));

        $content = $response->getContent();

        $response->setContent($content)->send();
    }

    private function loadMiddlewares()
    {
        if(file_exists($this->paths['app'] . '/middlewares.php')) {
            $this->middlewares = include_once($this->paths['app'] . '/middlewares.php');
        } else {
            $this->middlewares = array();
        }
    }

    private function loadRouteFiles()
    {
        $routeFiles = Finder::create()->files()->name('*.php')->in($this->paths['app'].'/Routes')->depth(0);

        $router = container('router');
        foreach($routeFiles as $file) {
            require $file->getRealPath();
        }
    }

    public function getEnv()
    {
        if (!env('ENVIRONMENT') && is_file($this->paths['env_file'])) {
            $dotenv = Dotenv::create($this->paths['base']);
            $dotenv->load();
        }

        return env('ENVIRONMENT') ?: 'production';
    }

    private function database()
    {
        if($this->container->has('database')) {
            $database = $this->container->get('database');
            $database->addConnection($this->container->get('config')->get('database.default'));
            $database->setAsGlobal();
            $database->bootEloquent();
        }
    }
}