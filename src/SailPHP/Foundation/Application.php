<?php


namespace SailPHP\Foundation;


use Illuminate\Support\Arr;

class Application extends \Illuminate\Container\Container
{
    const VERSION = "1.0.0";

    protected $basePath;

    protected $appPath;

    protected $storagePath;

    protected $environmentPath;

    protected $environmentFile = '.env';

    protected $serviceProviders = [];

    protected $loadedProviders = [];

    protected $isConsole;

    protected $namespace;

    protected $booted = false;

    public function __construct($basePath = null)
    {
        if($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
    }

    public function version()
    {
        return static::VERSION;
    }

    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);
    }

    protected function registerBaseServiceProviders()
    {
        $this->register(new RoutingServiceProvider($this));
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->bindPathsInContainer();

        return $this;
    }

    protected function bindPathsInContainer()
    {
        $this->instance('path', $this->path());
        $this->instance('path.base', $this->basePath());
        $this->instance('path.config', $this->configPath());
    }

    public function path($path = '')
    {
        $appPath = $this->appPath ?: $this->basePath.DIRECTORY_SEPARATOR.'app';

        return $appPath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function basePath($path = '')
    {
        return $this->basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    public function register($provider, $force = false)
    {
        if(($registered = $this->getProvider($provider)) && !$force) {
            return $registered;
        }

        if(is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();

        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        if($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    public function getProvider($provider)
    {
        return array_values((array)$this->getProviders($provider))[0] ?? null;
    }

    public function getProviders($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        Arr::where($this->serviceProviders, function($value) use($name) {
            return $value instanceof $name;
        });
    }

    public function resolveProvider($provider)
    {
        return new $provider($this);
    }

    public function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;

        $this->loadedProviders[get_class($provider)] = true;
    }

    protected function bootProvider(ServiceProvider $provider)
    {
        if(method_exists($provider, 'boot')) {
            return $this->call([$provider, 'boot']);
        }
    }

    public function isBooted()
    {
        return $this->booted;
    }
}
