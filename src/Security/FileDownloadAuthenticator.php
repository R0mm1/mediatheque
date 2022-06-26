<?php

namespace App\Security;

use App\Entity\Book\ElectronicBook\FileDownloadToken as ElectronicFileDownloadToken;
use App\Entity\Book\AudioBook\FileDownloadToken as AudioFileDownloadToken;
use App\Entity\Mediatheque\FileDownloadToken;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FileDownloadAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return is_string($request->query->get('t'));
    }

    public function authenticate(Request $request): Passport
    {
        $route = $request->attributes->get('_route');
        $token = $request->query->get('t');

        $tokenClass = match ($route) {
            'api_book_files_get_item' => ElectronicFileDownloadToken::class,
            'api_audio_book_files_get_item' => AudioFileDownloadToken::class,
            default => throw new LogicException("No token class known for route " . print_r($route, true))
        };

        $tokenObject = $this->entityManager->getRepository($tokenClass)->findOneBy([
            'token' => $token
        ]);

        if (!$tokenObject instanceof FileDownloadToken) {
            $this->logger->alert(
                "Invalid file download token used",
                [
                    "download_token" => $token,
                    "route" => $route,
                ]
            );
            throw new AuthenticationException("Invalid token");
        }

        $creationDate = $tokenObject->getCreated();
        if (!$creationDate instanceof \DateTime || ($age = (time() - $creationDate->getTimestamp()) / 60) > 10) {
            $this->logger->info(
                "Expired file download token used",
                [
                    "download_token" => $token,
                    "age" => $age ?? 'unknown',
                    "route" => $route,
                    "user_sub" => $tokenObject->getUser()->getSub()
                ]
            );
            $this->entityManager->remove($tokenObject);
            $this->entityManager->flush();
            throw new AuthenticationException("Expired token");
        }

        $this->entityManager->remove($tokenObject);
        $this->entityManager->flush();

        $this->logger->info(
            "File download token used",
            [
                "download_token" => $token,
                "route" => $route,
                "user_sub" => $tokenObject->getUser()->getSub()
            ]
        );

        return new SelfValidatingPassport(new UserBadge(
            $tokenObject->getUser()->getId()
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
