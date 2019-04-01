<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 3:28 PM
 */

namespace SailPHP\Database;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Migrator
{
    private $config;

    private $tableName;

    public function __construct()
    {
        $this->config = config()->get('database');
        $this->filesystem = new Filesystem;
        $this->finder = new Finder;

        $this->path = root_path() . 'migrations/';
        $this->table = 'migrations';
    }
}