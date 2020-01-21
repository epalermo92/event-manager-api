<?php declare(strict_types=1);


namespace AppBundle\Exceptions;


use Throwable;

class NotOfTheSameTypeException extends \LogicException
{
    public function __construct($message , $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(string $message): self
    {
        return new self($message);
    }
}
