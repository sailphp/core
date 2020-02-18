<?php


namespace SailPHP\Auth\Adapters;


use SailPHP\Session\Session;
use SailPHP\Auth\Authable;

interface AuthAdapter
{
    public function setConfig(array $config);
    public function setSession(Session $session);
    public function login(Authable $authable);
    public function refresh();
    public function logout();
    public function loggedIn();
    public function user();
}