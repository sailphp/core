<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:32 PM
 */

namespace SailPHP\Session;

use SailPHP\Traits\AdapterPattern;

class Session
{
    use AdapterPattern;

    protected $adapter;

    protected $id;

    public function __construct(SessionAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function serialize($data)
    {
        return serialize($data);
    }

    public function unserialize($text)
    {
        return unserialize($text);
    }
}