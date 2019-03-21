<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Specification\None;
use App\Specification\Order;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserController extends AbstractController
{
    /**
     * @Route("/api/user/", name="api_list_user", methods="GET")
     */
    public function listUser()
    {
        /**@var $userRepository UserRepository */
        $userRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(User::class);

        /**@var $aUser User[] */
        $aUser = $userRepository->match(new None(), ['username' => 'ASC']);

        $aReturn = [];
        foreach ($aUser as $user) {
            $aReturn[$user->getId()] = $user->getUsername();
        }

        return $this->json($aReturn);
    }

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
        } else {
            return $this->json([
                'password' => 'Les mots de passe ne correspondent pas'
            ], 400);
        }
    }
}