<?php

namespace SailPHP\Mail;

use SailPHP\Traits\AdapterPattern;

class Mailer 
{ 
    use AdapterPattern;

    protected $adapter;

    public function __construct(MailAdapter $mailer)
    {
        $this->adapter = $mailer;
    }
}