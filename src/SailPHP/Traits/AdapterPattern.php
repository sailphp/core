<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:34 PM
 */

namespace SailPHP\Traits;

trait AdapterPattern
{
    public function __call($method, $arguements)
    {
        if(!method_exists($this->getAdapter(), $method)) {
            throw new \Exception("Method: " . $method . " wasn't found on the adapter.");
        }

        return $this->getAdapter()->$method(...$arguements);
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

}