<?php

namespace App\Security;

use App\Entity\User;
use App\Security\Exception\ExpiredTokenException;
use App\Security\Exception\InvalidTokenException;
use App\Service\TokenDecoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OAuth2Authenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly string                 $keycloakExpectedIssuer,
        private readonly TokenDecoderInterface  $tokenDecoder,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationString = $request->headers->get('Authorization');

        try {
            [, $encodedJwt] = explode(' ', $authorizationString);

            $jwtPayload = $this->tokenDecoder->decode($encodedJwt);
        } catch (ExpiredException $exception) {
            $this->logger->alert(
                "Attempt to login with an expired token",
                [
                    'Authorization' => $authorizationString
                ]
            );
            throw new ExpiredTokenException("Token expire", 0, $exception);
        } catch (\Throwable $exception) {
            $this->logger->alert(
                $exception->getMessage(),
                [
                    'Authorization' => $authorizationString
                ]
            );
            throw InvalidTokenException::get();
        }


        if (!isset($jwtPayload->iss) || $jwtPayload->iss !== $this->keycloakExpectedIssuer) {
            $this->logger->alert(
                "Attempt to log in using a token with an invalid issuer",
                [
                    'Authorization' => $authorizationString,
                    'JwtPayload' => $jwtPayload
                ]
            );
            throw InvalidTokenException::get();
        }

        return new SelfValidatingPassport(new UserBadge(
            $jwtPayload->sub,
            function ($sub) use ($jwtPayload) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy([
                    'sub' => $sub
                ]);

                if (!$user instanceof User) {
                    $user = new User($sub);
                    $user->setFirstname($jwtPayload->given_name ?? '');
                    $user->setLastname($jwtPayload->family_name ?? '');

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            }
        ));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
