<?php

namespace App\Service\JWT;

interface JWTInterface
{
    public function decode(string $jwt, $keyOrKeyArray): object;
}
