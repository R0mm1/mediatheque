<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route(name="api_security_login", path="/api/security/login")
     * @return JsonResponse
     */
    public function apiLogin(): JsonResponse
    {
        /**@var $user User */
        $user = $this->getUser();

        return new JsonResponse([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }
}
