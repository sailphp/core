<?php


namespace SailPHP\Auth;


use SailPHP\Session\Session;

interface AuthAdapter
{
    public function setConfig(array $config);
    public function setSession(Session $session);
    public function login(Authable $authable);
    public function logout();
    public function loggedIn();
    public function user();
}