<?php

namespace App\Security\Exception;


use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExpiredTokenException extends AuthenticationException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        if (strlen($message) > 0) {
            $message = " : $message";
        }
        parent::__construct("Expired token$message", $code, $previous);
    }
}
