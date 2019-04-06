<?php

namespace SailPHP\Auth;

use SailPHP\Session\SessionAdapter;
use SailPHP\Session\Session;
use SailPHP\Traits\AdapterPattern;

class Auth
{
    use AdapterPattern;

    protected $session;

    protected $config;

    protected $adapter;

    public function __construct(AuthAdapter $adapter, Session $session = null)
    {
        $this->config = config()->get('auth');
        $this->adapter = $adapter;
        $this->session = is_null($session) ? session() : $session;

        $this->adapter->setConfig($this->config);
        $this->adapter->setSession($this->session);
    }
}