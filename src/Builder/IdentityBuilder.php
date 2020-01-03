<?php

use http\Exception;

class IdentityBuilder
{


    public static function build($name, $surname, $type)
    {
        if (!is_string($name))
        {
            throw new RuntimeException($name . 'must be instance of string, ' . gettype($name) . ' given.');
        }
        if (!is_string($surname))
        {
            throw new Exception\RuntimeException($surname . 'must be instance of string, ' . gettype($surname) . ' given.');
        }
        if (!is_string($type))
        {
            throw new Exception\RuntimeException($type . 'must be instance of string, ' . gettype($type) . ' given.');
        }

        if (!in_array(strtoupper($type), ['L', 'N']))
        {
            throw new Exception\RuntimeException('Invalid Identity Type.');
        }

        if ($type === 'L')
        {
            return new LegalIdentity($name, $surname);
        }
        return new NaturalIdentity($name, $surname);
    }
}