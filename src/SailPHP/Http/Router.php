<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 12:20 PM
 */

namespace SailPHP\Http;


use SailPHP\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
/**
 * Class to register your application routes with,
 * bound to application service.
 */
class Router extends RouteCollection
{
    /**
     * Stores the request context for
     * this router.
     *
     * @var Symfony\Component\Routing\RequestContext
     */
    protected $context;
    /**
     * The resource methods with matching names
     * and route patterns.
     *
     * @var array
     */
    private static $resource_methods = [
        'create' => ['POST', '/'],
        'read'   => ['GET', '/{id}'],
        'update' => ['PUT', '/{id}'],
        'delete' => ['DELETE', '/{id}'],
    ];

    private $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];

    protected $path = null;
    protected $name = null;

    protected $groupStack = array();

    private $middlewares = [];
    /**
     * @param array $attributes
     */
    public function updateGroupStack(array $attributes)
    {
        $this->groupStack = $attributes;
    }

    /**
     * @return mixed|string
     */
    public function getGroupPrefix()
    {
        return isset($this->groupStack['prefix']) ? $this->groupStack['prefix'] : '';
    }

    /**
     * @param $uri
     * @return string
     */
    protected function prefix($uri)
    {
        return '/' . trim(trim($this->getGroupPrefix(), '/') . '/' . trim($uri, '/'), '/') ? : '/';
    }

    public function group(array $attributes, \Closure $callback)
    {
        $this->updateGroupStack($attributes);
        call_user_func($callback, $this);
        array_pop($this->groupStack);
    }

    /**
     * Handle calling get(), post(), put(), delete() etc
     * on this router.
     *
     * @param  string $method
     * @param  string $path
     * @param  array  $options
     * @return void
     *
     * @throws UnsupportedMethodException
     * @throws InvalidNumberOfArgumentsException
     */
    public function route($method, $path, array $options)
    {
        $method = strtoupper($method);

        if($method != '*') {
            if (!in_array($method, $this->allowedMethods)) {
                throw new UnsupportedMethodException($method);
            }
        }

        $path = str_replace(':int', '<\d+>', $path);
        $path = str_replace(':slug', '<[a-zA-Z0-9-_]+>', $path);
        $path = $this->prefix($path);

        $methodArr = [];
        if($method != '*') {
            $methodArr = [$method];
        } else {
            $methodArr = $this->allowedMethods;
        }

        $routeName = $options['name'];
        $this->name = $routeName;
        $options = ['controller' => $options['controller']];
        $route = new Route($path, $options, [], [], '', [], $methodArr);
        $this->add($routeName, $route);
        $this->path = $path;
        return $this;
    }

    /**
     * Add a resource route using CRUD/REST
     * endpoint patterns and request methods.
     *
     * Can optionally pass array of names, first index
     * is used as singular, second as plural.
     *
     * @param  string|array $name
     * @param  array        $allowed
     * @param  string       $namespace
     */
    public function resource($name, $allowed = ['create', 'read', 'update', 'delete'], $namespace = '\App\Controllers')
    {
        $methods = [];
        foreach ($allowed as $allowed_method) {
            if (isset(static::$resource_methods[$allowed_method])) {
                $methods[$allowed_method] = static::$resource_methods[$allowed_method];
            }
        }
        $singular = $name;
        $plural = $name;
        if (is_array($name) && count($name) >= 2) {
            $singular = $name[0];
            $plural   = $name[1];
        }
        foreach ($methods as $type => $route) {
            $url = '/' . $plural . $route[1];
            $route_name = join(', ', [strtolower($singular), $type]);
            $controller = trim(ucfirst($plural));
            $this->route($route[0], $url, [
                'name'       => $route_name,
                'controller' => $namespace . '\\' . $controller .'Controller@' . $type,
            ]);
        }
    }

    /**
     * Matches the routes setup by the user against
     * the current request.
     *
     * @return array|null
     */
    public function match(SymfonyRequest $request)
    {
        $context = $this->context();
        $path = $request->getPathInfo();
        $matcher = new UrlMatcher($this, $context);
//        dd($this);
        try {
            $middleware = $matcher->match($path);
        } catch(ResourceNotFoundException $e) {
            return null;
        }

        $this->matchMiddleware($middleware);

        return $middleware;
    }

    private function matchMiddleware($middleware)
    {
        if(empty($middleware)) {
            return;
        }
        $name = $middleware['_route'];

        if(!\array_key_exists($name, $this->middlewares)) {
            return;
        }

        $match = $this->middlewares[$name];
        if(empty($match) || is_null($match)) {
            return;
        }

        return $this->sortMiddleware($match);
    }

    private function sortMiddleware($middleware)
    {
        $appMiddlewares = container('app')->middlewares();

        if(empty($middleware) || empty($appMiddlewares)) {
            return;
        }
        $name = $middleware['name'];
        $params = $middleware['params'];
        if(array_key_exists($name, $appMiddlewares)) {
            $mapped = $appMiddlewares[$name];
            if(!is_null($mapped)) {
                return $this->runMiddleware($mapped, $params);
            }
        }

        $this->runMiddleware($name, $params);
    }

    private function runMiddleware($class, $params)
    {
        $class = new $class;
        return call_user_func_array(array($class, 'handle'), $params);
    }

    /**
     * Get the request context for this router.
     *
     * @return RequestContext
     */
    public function context()
    {
        if (!$this->context) {
            $this->context = new RequestContext();
            $this->context->fromRequest(
                request()
            );
        }
        return $this->context;
    }
    /**
     * Get the path for a route by name.
     *
     * @param  $name
     * @return string
     */
    public function path($name, $requirements = [])
    {
        $route = $this->get($name);
        if (!is_null($route)) {
            $generator = new UrlGenerator($this, $this->context());

            return $generator->generate($name, $requirements);
        }
        return '/';
    }

    public function middleware($middleware, $params = array()) {
        if(!array_key_exists($this->name, $this->middlewares)) {
            $this->middlewares[$this->name] = array('name'  => $middleware, 'params'    => $params);
        }

        $data = $this->middlewares[$this->name];

        array_push($data, $middleware);

        return $this;
    }
}
