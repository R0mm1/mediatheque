<?php

namespace App\Service;

interface TokenDecoderInterface
{
    public function decode(string $encodedToken): object;
}
