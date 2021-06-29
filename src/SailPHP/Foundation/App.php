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
use Dotenv\Repository\RepositoryBuilder;
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
            echo view('errors/404', array(

            ));
        } catch(\Throwable $e) {
            header("HTTP/1.0 404 Not Found");
            echo '<h1>404 Not Found</h1>';
        }

        die();
    }

    public function addConnection($name) {
        if($this->container->has('database')) {
            $database = $this->container->get('database');
            $database->addConnection($this->container->get('config')->get('database.'.$name), $name);
            $this->database = $database;
        }
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

    public function cli()
    {
        $this->database();
    }

    public function listen()
    {

        $this->loadMiddlewares();
        if($this->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRouteFiles();
        }
        $router = $this->container->get('router');
        $request = $this->container->get('request');

        try {
            $match = $router->match($request);
            $route = new Route($match);

            $this->container->bind('response', $route->match());
        } catch(\Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {
            
            header($_SERVER["SERVER_PROTOCOL"]." 405 Method Not Allowed", true, 405);

            $template = $this->container->get('template');
            $extension = $template->getExtension();

            $file = paths('base').'/templates/errors/404.' . $extension;
            if(file_exists($file)) {
                $this->container->get('template')->render('errors/404', []);
                exit;
            } 

            die('404');
        }
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
        $repository = RepositoryBuilder::createWithDefaultAdapters()->make();

        if (!env('ENVIRONMENT') && is_file($this->paths['env_file'])) {
            $dotenv = Dotenv::create($repository, $this->paths['base']);
            $dotenv->load();
        }

        return env('ENVIRONMENT') ?: 'production';
    }

    public function database()
    {
        if($this->container->has('database')) {
            $database = $this->container->get('database');
            $database->addConnection($this->container->get('config')->get('database.default'));
            $database->setAsGlobal();
            $database->bootEloquent();
        }
    }

    public function routesAreCached()
    {
        return $this->cacheFileExists('routes.php');
    }

    private function cacheFileExists($file)
    {
        if(!in_array('cache', $this->paths)) {
            return false;
        }

        if(file_exists($this->paths['cache'] . '/' . $file)) {
            return true;
        }

        return false;
    }

    private function loadCachedRoutes()
    {

    }
}
