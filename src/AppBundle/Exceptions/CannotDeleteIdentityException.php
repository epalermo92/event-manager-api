<?php declare(strict_types=1);


namespace AppBundle\Exceptions;


use Throwable;

class CannotDeleteIdentityException extends \LogicException
{
    public function __construct($message = 'You cannot delete this identity because is an event organizer.  ', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): self
    {
        return new self();
    }
}
