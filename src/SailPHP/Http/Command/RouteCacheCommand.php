<?php

namespace SailPHP\Model\Command;

use SailPHP\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Create
 * @package SailPHP\Model\Command
 */
class RouteCacheCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'route:cache';

    /**
     * @var string
     */
    protected $description = "Create a route cache file for faster route registration.";

    /**
     * @var string
     */
    protected $help = "Create a route cache file for faster route registration.";

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }
}
