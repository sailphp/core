<?php

namespace SailPHP\Mail;

use Swift_Mailer;
use SailPHP\Mail\Mailer;

class Mail 
{
    protected $swift;

    public function __construct()
    {   
        $transport = (new \Swift_SmtpTransport());

        $this->swift = new Swift_Mailer($transport);

        //return new Mailer($this->swift);
    }
}