<?php


namespace SailPHP\Auth;


trait AuthableTrait
{
    public function serialize()
    {
        return (object)[
            'id'    => $this->id
        ];
    }
}