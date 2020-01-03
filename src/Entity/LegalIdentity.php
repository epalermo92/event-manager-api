<?php


class LegalIdentity extends AbstractIdentity
{
    public function __construct($name, $surname)
    {
        parent::__construct($name, $surname, 'L');
    }
}