<?php

namespace App\Service\JWT;

class JWT implements JWTInterface
{

    public function decode($jwt, $keyOrKeyArray, array $allowed_algs = array()): object
    {
        return \Firebase\JWT\JWT::decode($jwt, $keyOrKeyArray, $allowed_algs);
    }
}
