<?php declare(strict_types=1);


namespace AppBundle\Exceptions;


use Throwable;

class NotOfTheSameTypeException extends \LogicException
{
    public function __construct($message = 'Trying to update a Natural/Legal Identity but sending Legal/Natural Identity data', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): self
    {
        return new self();
    }
}
