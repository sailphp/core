<?php

namespace SailPHP\Mail;

use Swift_Mailer;

class Mail 
{
    protected $swift;

    public function __construct()
    {
        $this->swift = new Swift_Mailer();

        return new Mailer($this->swift);
    }
}