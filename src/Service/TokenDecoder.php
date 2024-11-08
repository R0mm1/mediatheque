<?php

namespace App\Service;

use App\Service\JWT\JWTInterface;
use Firebase\JWT\CachedKeySet;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Psr\Http\Client\ClientInterface;

class TokenDecoder implements TokenDecoderInterface
{
    public function __construct(
        private string          $keycloakInternalBaseUrl,
        private JWTInterface    $jwt,
        private ClientInterface $client,
    )
    {
    }

    public function decode(string $encodedToken, int $attemptCounter = 0): object
    {
        $keySet = new CachedKeySet(
            sprintf(
                '%s/realms/mediatheque/protocol/openid-connect/certs',
                $this->keycloakInternalBaseUrl
            ),
            $this->client,
            new Psr17Factory(),
            new ApcuAdapter('jwks')
        );

        return $this->jwt->decode($encodedToken, $keySet);
    }
}
