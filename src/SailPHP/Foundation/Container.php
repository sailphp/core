<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 11:07 AM
 */

namespace SailPHP\Foundation;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class Container
 * @package SailPHP\Foundation
 */
class Container extends ContainerBuilder
{
    /**
     * @var
     */
    public static $instance;

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new Container();
        }
        return static::$instance;
    }

    /**
     * @param $service
     * @param $instance
     * @return $this
     */
    public function bind($service, $instance)
    {
        $this->set($service, $instance);

        return $this;
    }
}
