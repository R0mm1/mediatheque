<?php

namespace App\Service\JWT;

interface JWKInterface
{
    public function parseKeySet(array $jwks): array;
}
