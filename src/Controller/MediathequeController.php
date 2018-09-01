<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MediathequeController extends AbstractController
{
    /**
     * @Route("/mediatheque", name="mediatheque")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MediathequeController.php',
        ]);
    }
}
