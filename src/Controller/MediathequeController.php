<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MediathequeController extends AbstractController
{
    /**
     * @Route("/", name="login_default")
     * @Route("/login", name="login")
     */
    public function index()
    {
        return $this->render('mediatheque/login.html.twig');
    }

    /**
     * @Route("/install", name="install")
     */
    public function install(){
        return $this->render('mediatheque/install.html.twig');
    }

    /**
     * @Route("/account", name="account")
     */
    public function account(){
        return $this->render('mediatheque/account.html.twig');
    }
}
