<?php

namespace App\Service\JWT;

class JWT implements JWTInterface
{

    public function decode(string $jwt, $keyOrKeyArray): object
    {
        return \Firebase\JWT\JWT::decode($jwt, $keyOrKeyArray);
    }
}
