<?php

namespace SailPHP\Http;

use ArrayAccess;
use Countable;
use Iterator;
use RuntimeException;

class Csrf
{
    protected $prefix;

    protected $strength;

    protected $storage;

    protected $keyPair = null;

    public function __construct($strength = 16, $prefix = "csrf")
    {
        if($strength < 16) {
            throw new RuntimeException("CSRF minimum strength needs to be 16.");
        }

        $this->strength = $strength;
        $this->prefix = rtrim($prefix, "_");
    }

    public function getToken()
    {
        if(!isset($_SESSION['_'.$this->prefix])) {
            return null;
        }

        return $_SESSION['_' . $this->prefix];
    }

    public function generate()
    {
        if(!isset($_SESSION['_'.$this->prefix])) {
            $token = $this->newToken();
        } else {
            $token = $_SESSION['_' . $this->prefix];     
        }
        
        return $token;
    }


    public function validate($value)
    {
        $token = $this->getToken();
        if(is_null($token)) {
            return false;
        }

        if(function_exists('hash_equals')) {
            return hash_equals($token, $value);
        }

        return $token === $value;
    }

    public function newToken()
    {
        $token = password_hash(random_bytes($this->strength), PASSWORD_BCRYPT);
        $_SESSION['_'.$this->prefix] = $token;

        return $token;
    }

    public function destroy()
    {
        $this->newToken();
    }
    
}
