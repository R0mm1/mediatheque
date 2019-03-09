<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/book", name="book")
     */
    public function indexBook()
    {
        return $this->render('book/book.html.twig');
    }
    /**
     * @Route("/author", name="author")
     */
    public function indexAuthor()
    {
        return $this->render('book/author.html.twig');
    }
}
