<?php declare(strict_types=1);


namespace AppBundle\Exceptions;


use Throwable;

class EntityNotBuiltException extends \LogicException
{
    public function __construct($message = 'Entity not built. ', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): self
    {
        return new self();
    }
}
