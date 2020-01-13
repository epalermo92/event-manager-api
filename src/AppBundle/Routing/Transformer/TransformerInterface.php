<?php


namespace AppBundle\Routing\Transformer;


use AppBundle\Entity\AbstractIdentity;
use Widmogrod\Monad\Either\Either;

interface TransformerInterface
{
    /**
     * @return Either
     */
    public function DoTransformValid();

    public function DoTransformInvalid(): Either;
}
