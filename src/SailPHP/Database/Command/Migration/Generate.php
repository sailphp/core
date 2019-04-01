<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:24 PM
 */

namespace SailPHP\Database\Console\Migration;

use SailPHP\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Generate extends Command
{
    protected $name = 'migrate:generate';
    protected $description = "Generate a new migration file, timestamped";
    protected $help = '';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $this->formatClassname($input->getArgument('name'));


    }
}