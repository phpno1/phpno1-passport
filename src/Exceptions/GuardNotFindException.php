<?php

namespace Phpno1\Passport\Exceptions;

use Exception;

class GuardNotFindException extends Exception
{
    protected const ERROR_MESSAGE = 'guard not find please check the configuration';

    public function __construct(string $message = null)
    {
        $message = $message ?? static::ERROR_MESSAGE;
        parent::__construct($message, 0);
    }
}