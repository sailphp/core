<?php

interface Mailable
{
    public function getName() : string;

    public function getEmail() : string;
}