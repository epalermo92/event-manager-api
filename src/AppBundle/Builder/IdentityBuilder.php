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


    public static function build($data): Either
    {

        if (!is_string($data['name'])) {
            return left(new RuntimeException($data['name'] . 'must be instance of string, ' . gettype($data['name']) . ' given.'));
        }
        if (!is_string($data['surname'])) {
            if ($data['surname'] !== null)
            {
                return left(new RuntimeException($data['surname'] . 'must be instance of string, ' . gettype($data['surname']) . ' given.'));
            }
        }
        if (!is_string($data['codice'])) {
            return left(new RuntimeException($data['codice'] . 'must be instance of string, ' . gettype($data['codice']) . ' given.'));
        }
        if (!is_string($data['type'])) {
            return left(new RuntimeException($data['type'] . 'must be instance of string, ' . gettype($data['type']) . ' given.'));
        }

        if (!in_array(strtoupper($data['type']), ['LEGAL', 'NATURAL'])) {
            return left(new RuntimeException('Invalid Identity Type.'));
        }

        if (strtoupper($data['type']) === 'LEGAL') {
            return right(new LegalIdentity($data['name'], $data['codice']));
        }

        if (strtoupper($data['type']) === 'NATURAL') {
            return right(new NaturalIdentity($data['name'], $data['surname'], $data['codice']));
        }
        return left(new RuntimeException('Invalid person type.'));
    }
}
