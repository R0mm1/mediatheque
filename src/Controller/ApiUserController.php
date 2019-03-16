<?php

namespace App\Controller;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserController extends AbstractController
{
    /**
     * @Route("/api/user/menu", name="api_user_menu", methods="GET")
     */
    public function getMenu()
    {
        return $this->json([
            [
                'label' => 'Livres',
                'children' => [
                    [
                        'label' => 'Par livre',
                        'target' => '/book'
                    ],
                    [
                        'label' => 'Par auteur',
                        'target' => '/author'
                    ]
                ]
            ]
        ]);
    }

    /**
     * @Route("/api/user/password", name="api_user_set_password", methods="PUT")
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function setPassword(Request $request, UserManagerInterface $userManager)
    {
        $params = $this->getParameters($request);

        if ($params['password'] === $params['confirmation']) {
            /**@var $user \App\Entity\User */
            $user = $this->getUser();
            $user->setPlainPassword($params['password']);
            $userManager->updateUser($user);
            return $this->json([]);
        }else{
            return $this->json([
                'password'=>'Les mots de passe ne correspondent pas'
            ], 400);
        }
    }
}