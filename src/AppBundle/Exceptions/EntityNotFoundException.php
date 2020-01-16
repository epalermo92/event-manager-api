<?php declare(strict_types=1);

namespace AppBundle\Exceptions;

use Throwable;

class EntityNotFoundException extends \LogicException
{
    public function __construct($message = 'Entity not found. ', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): EntityNotFoundException
    {
        return new self();
    }
}
