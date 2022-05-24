<?php

namespace App\Service;

use App\Service\JWT\JWKInterface;
use App\Service\JWT\JWTInterface;
use Firebase\JWT\SignatureInvalidException;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TokenDecoder implements TokenDecoderInterface
{
    private const CACHE_JWKS_KEY = 'jwks';

    public function __construct(
        private string              $keycloakInternalBaseUrl,
        private JWKInterface        $jwk,
        private JWTInterface        $jwt,
        private HttpClientInterface $client,
    )
    {
    }

    public function decode(string $encodedToken, int $attemptCounter = 0): object
    {
        $cache = new ApcuAdapter('authentication');
        $isFresh = !$cache->hasItem(self::CACHE_JWKS_KEY);
        $jwks = $cache->get(self::CACHE_JWKS_KEY, function () {
            $jwksResponse = $this->client->request(
                'GET',
                sprintf(
                    '%s/realms/mediatheque/protocol/openid-connect/certs',
                    $this->keycloakInternalBaseUrl
                )
            );

            return json_decode($jwksResponse->getContent(), true);
        });

        try {
            $jwtPayload = $this->jwt->decode($encodedToken, $this->jwk->parseKeySet($jwks), ['RS256']);

        } catch (SignatureInvalidException $exception) {
            if ($isFresh || $attemptCounter >= 1) {
                throw $exception;
            }

            $cache->delete(self::CACHE_JWKS_KEY);
            $jwtPayload = $this->decode($encodedToken, $attemptCounter++);
        }

        return $jwtPayload;
    }
}
