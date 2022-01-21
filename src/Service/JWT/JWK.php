<?php

namespace App\Service\JWT;

class JWK implements JWKInterface
{

    public function parseKeySet(array $jwks): array
    {
        return \Firebase\JWT\JWK::parseKeySet($jwks);
    }
}
