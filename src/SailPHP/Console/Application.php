<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:18 PM
 */

namespace SailPHP\Console;

use SailPHP\Foundation\App;
use Symfony\Component\Console\Application as Console;

class Application extends Console
{
    protected $config;

    protected $app;

    public function __construct($root, array $providers)
    {
        parent::__construct();

        $this->beep($root, $providers);

        $this->config = [
            'commands'  => [
                \SailPHP\Model\Command\Create::class,
                \SailPHP\Controller\Command\Create::class
            ],
        ];

        foreach($this->config['commands'] as $command) {
            $instance = new $command;
            $this->add($instance);
        }
    }

    private function beep($root, array $providers)
    {
        $this->app = new App($root, $providers, true);
    }
}