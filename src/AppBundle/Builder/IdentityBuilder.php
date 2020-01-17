<?php declare(strict_types=1);

namespace AppBundle\Builder;

use AppBundle\Entity\AbstractIdentity;
use AppBundle\Entity\LegalIdentity;
use AppBundle\Entity\NaturalIdentity;
use AppBundle\Exceptions\EntityNotBuiltException;
use Widmogrod\Monad\Either\Either;
use function Widmogrod\Monad\Either\left;
use function Widmogrod\Monad\Either\right;

class IdentityBuilder
{
    public static function build($data): Either
    {
        switch ($data['type']) {
            case AbstractIdentity::LEGAL:
                return right(new LegalIdentity($data['name'], $data['partitaIva']));
                break;
            case AbstractIdentity::NATURAL:
                return right(new NaturalIdentity($data['name'], $data['surname'], $data['codiceFiscale']));
                break;
            default:
                return left(new EntityNotBuiltException());
        }
    }
}
