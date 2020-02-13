<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:32 PM
 */

namespace SailPHP\Session;

use SailPHP\Traits\AdapterPattern;

/**
 * Class Session
 * @package SailPHP\Session
 */
class Session
{
    use AdapterPattern;

    /**
     * @var SessionAdapter
     */
    protected $adapter;

    /**
     * @var
     */
    protected $id;

    /**
     * Session constructor.
     * @param SessionAdapter $adapter
     */
    public function __construct(SessionAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param $data
     * @return string
     */
    public function serialize($data)
    {
        return serialize($data);
    }

    /**
     * @param $text
     * @return mixed
     */
    public function unserialize($text)
    {
        return unserialize($text);
    }
}
