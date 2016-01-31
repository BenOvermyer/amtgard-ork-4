<?php namespace Ork;

class Ork3LibContainer
{
    public function __construct()
    {

    }

    public function __set( $name, $value )
    {
        $this->$name = $value;
    }
}