<?php

namespace SailPHP\Mail;

class Pending
{
    protected $mailer;

    protected $to = [];

    protected $cc = [];

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function to($users) 
    {
        $this->to = $users;

        return $this;
    }

    public function cc($users) 
    {
        $this->cc = $users;

        return $this;
    }

    public function send(Mailable $mailable)
    {
        return $this->mailer->send($this->fill($mailable));
    }

    protected function fill(Mailable $mailable)
    {
        return $mailable->to($this->to)
                        ->cc($this->cc);
    }
}