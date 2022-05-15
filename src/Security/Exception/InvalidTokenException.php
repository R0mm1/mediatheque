<?php

namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class InvalidTokenException extends AuthenticationException
{
    private function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function get(){
        return new self("Invalid token");
    }
}
