<?php declare(strict_types=1);

namespace AppBundle\Exceptions;

use Throwable;

class FormNotValidException extends \LogicException
{
    public function __construct($message = 'Form is not valid!', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): FormNotValidException
    {
        return new self();
    }
}
