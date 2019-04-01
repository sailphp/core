<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:36 PM
 */

namespace SailPHP\Session;


use http\Exception\RuntimeException;

class SessionAdapter
{
    protected $id;

    public function delete($key)
    {
        $this->checkHasStarted();
        unset($_SESSION[$key]);

        return $this;
    }

    public function put($key, $value)
    {
        $this->checkHasStarted();
        return $_SESSION[$key] = $value;
    }

    public function has($key)
    {
        $this->checkHasStarted();
        return isset($_SESSION[$key]);
    }

    public function get($key)
    {
        $this->checkHasStarted();
        $value = $_SESSION[$key];

        // Check for quick
        if($this->has('quick::' . $key)) {
            $this->delete($key);
        }

        return $value;
    }

    public function quick($key, $value)
    {
        $this->checkHasStarted();
        $this->put($key, $value);
        $this->put('quick::' . $key, true);

        return $value;
    }

    public function start()
    {
        session_start();

        $this->id = session_id();
    }

    public function refresh()
    {
        session_regenerate_id();
    }

    private function checkHasStarted()
    {
        $status = session_status();

        if($status != PHP_SESSION_ACTIVE) {
            throw new \RuntimeException();
        }
    }
}