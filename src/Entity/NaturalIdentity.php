<?php


class NaturalIdentity extends AbstractIdentity
{
    public function __construct($name, $surname)
    {
        parent::__construct($name, $surname, 'N');
    }
}