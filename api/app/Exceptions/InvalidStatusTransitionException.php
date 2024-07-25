<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidStatusTransitionException extends Exception
{
    public function __construct($message = 'Invalid status transition', $code = Response::HTTP_UNPROCESSABLE_ENTITY, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
