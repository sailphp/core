<?php


namespace SailPHP\Auth;


trait AuthableTrait
{
    public function serialize($field = 'id')
    {
        return (object)[
            'id'    => $this->{$field}
        ];
    }
}