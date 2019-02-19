<?php

namespace App\Controller;

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
}