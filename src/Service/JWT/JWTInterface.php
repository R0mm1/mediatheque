<?php

namespace App\Service\JWT;

interface JWTInterface
{
public function decode($jwt, $keyOrKeyArray, array $allowed_algs = array()):object;
}
