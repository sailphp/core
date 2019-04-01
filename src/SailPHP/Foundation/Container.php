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

class Container extends ContainerBuilder
{
    public static $instance;

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new Container();
        }
        return static::$instance;
    }

    public function bind($service, $instance)
    {
        $this->set($service, $instance);

        return $this;
    }
}