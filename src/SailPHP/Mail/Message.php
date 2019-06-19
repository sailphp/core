<?php

namespace SailPHP\Mail;

class Message
{
    protected $swift;

    public function __construct($swift)
    {
        $this->swift = $swift;
    }

    public function from($email, $name = null)
    {
        $this->swift->setFrom($email, $name);

        return $this;
    }

    public function sender($email, $name = null)
    {
        $this->swift->setSender($email, $name);

        return $this;
    }

    public function to($email, $name = null, $override = false)
    {
        if($override) {
            $this->swift->setTo($email, $name);
            return $this;
        }

        return $this->addEmails($email, $name, 'to');
    }

    public function subject($subject)
    {
        $this->swift->setSubject($subject);

        return $this;
    }

    protected function addEmails($email, $name, $type)
    {
        if(is_array($email)) {
            $this->swift->{"set{$type}"}($email, $name);
        } else {
            $this->swift->{"add{$type}"}($email, $name);
        }

        return $this;
    }

    public function getSwiftMessage()
    {
        return $this->swift;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->swift, $method, $parameters);
    }
}