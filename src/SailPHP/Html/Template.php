<?php
/**
 * Created by PhpStorm.
 * User: Brad
 * Date: 1/04/2019
 * Time: 2:14 PM
 */

namespace SailPHP\Html;


use RuntimeException;

class Template
{
    private $loader = null;

    private $twig = null;

    public function __construct()
    {

        $this->loader = new \Twig\Loader\FilesystemLoader(paths('base').'/templates');
        $this->twig = new \Twig\Environment($this->loader);
    }

    public function render($file, $parameters = array())
    {
        echo $this->twig->render($file.".html", $parameters);
    }
}