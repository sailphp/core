<?php

namespace SailPHP\Session\Adapters;

interface SessionAdapter
{
    public function delete($key);
    public function put($key, $value);
    public function quick($key, $value);
    public function has($key);
    public function get($key);
    public function id();
    public function start();
    public function refresh();
}
