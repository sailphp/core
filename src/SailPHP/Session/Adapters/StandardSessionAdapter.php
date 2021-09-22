<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:36 PM
 */

namespace SailPHP\Session\Adapters;


use http\Exception\RuntimeException;

/**
 * Class SessionAdapter
 * @package SailPHP\Session\Adapters
 */
class StandardSessionAdapter implements SessionAdapter
{
    /**
     * @var
     */
    protected $id;

    /**
     * @param $key
     * @return $this
     */
    public function delete($key)
    {
        $this->checkHasStarted();
        unset($_SESSION[$key]);

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function put($key, $value)
    {
        $this->checkHasStarted();
        return $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $this->checkHasStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        $this->checkHasStarted();

        if(!$this->has($key)) {
            return null;
        }
        $value = $_SESSION[$key];

        // Check for quick
        if($this->has('quick::' . $key)) {
            $this->delete($key);
        }

        return $value;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function quick($key, $value)
    {
        $this->checkHasStarted();
        $this->put($key, $value);
        $this->put('quick::' . $key, true);

        return $value;
    }
    public function id()
    {
        return $this->id;
    }

    /**
     *
     */
    public function start()
    {
        session_start();
        $this->id = session_id();
    }

    /**
     *
     */
    public function refresh()
    {
        session_regenerate_id();
    }

    public function destroy()
    {
        session_destroy();
    }

    /**
     *
     */
    private function checkHasStarted()
    {
        $status = session_status();

        if($status != PHP_SESSION_ACTIVE) {
            throw new \RuntimeException();
        }
    }
}
