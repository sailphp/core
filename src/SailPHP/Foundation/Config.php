<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 11:27 AM
 */

namespace SailPHP\Foundation;

use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;

class Config extends Repository
{
    public function loadConfigurationFiles($path, $env = null)
    {
        $this->configPath = $path;

        foreach($this->getConfigurationFiles() as $fileKey => $path) {
            $this->set($fileKey, require $path);
        }

        foreach($this->getConfigurationFiles($env) as $fileKey => $path) {
            $envConfig = require $path;

            foreach($envConfig as $envKey => $value) {
                $this->set($fileKey .'.'. $envKey, $value);
            }
        }
    }

    protected function getConfigurationFiles($env = null)
    {
        $path = $this->configPath;

        if($env) {
            $path .=  $env;
        }

        if(!is_dir($path)) {
            return [];
        }

        $files = [];
        $phpFiles = Finder::create()->files()->name('*.php')->in($path)->depth(0);

        foreach($phpFiles as $file) {
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }
}