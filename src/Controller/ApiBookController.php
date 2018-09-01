<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiBookController extends AbstractController
{
    /**
     * @Route("/api/book", name="api_book", methods="GET")
     */
    public function index()
    {
        $aBook = $this->getDoctrine()
            ->getRepository(Book::class)
            ->findAll();

        $return = [];
        /**@var $book \App\Entity\Book */
        foreach ($aBook as $book) {
            $aBookData= [
                'title' => $book->getTitle(),
                'year' => $book->getYear(),
                'language' => $book->getLanguage(),
                'authors'=>array()
            ];

            /**@var \App\Entity\Author*/
            foreach ($book->getAuthors() as $author) {
                $aBookData['authors'][] = [
                  'firstname'=>$author->getFirstname(),
                  'lastname'=>$author->getLastname()
                ];
            }

            $return[] = $aBookData;
        }

        return $this->json($return);
    }
}
