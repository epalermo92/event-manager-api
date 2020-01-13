<?php

namespace AppBundle\Builder;

use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use Symfony\Component\Security\Core\Exception\RuntimeException;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Monad\Either\left;
use function Widmogrod\Monad\Either\right;

class IdentityBuilder
{


    public static function build($type, $name, $surname , $codice): Either
    {
        if (!is_string($name)) {
            return left(new RuntimeException($name . 'must be instance of string, ' . gettype($name) . ' given.'));
        }
//        if (!is_string($surname)) {
//            return left(new RuntimeException($surname . 'must be instance of string, ' . gettype($surname) . ' given.'));
//
//        }
        if (!is_string($codice)) {
            return left(new RuntimeException($codice . 'must be instance of string, ' . gettype($codice) . ' given.'));
        }
        if (!is_string($type)) {
            return left(new RuntimeException($type . 'must be instance of string, ' . gettype($type) . ' given.'));
        }

        if (!in_array(strtoupper($type), ['legal', 'natural'])) {
            return left(new RuntimeException('Invalid Identity Type.'));
        }

        if ($type === 'legal') {
            return right(new LegalIdentity($name, $codice));
        }

        if ($type === 'natural') {
            return right(new NaturalIdentity($name, $surname, $codice));
        }
        return left(new RuntimeException('Invalid person type.'));
    }
}
