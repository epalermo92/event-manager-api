<?php


namespace AppBundle\Exceptions;


use Symfony\Component\Form\Exception\LogicException;
use Throwable;

class FormNotSubmittedException extends LogicException
{
    public function __construct($message = ' Error code 400. The form was not submitted.', $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function create(): FormNotSubmittedException
    {
        return new self;
    }
}
