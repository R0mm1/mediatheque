<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiBookController extends AbstractController
{
    /**
     * @Route("/api/book", name="api_book", methods="GET")
     */
    public function index(Request $request)
    {
        /**@var $qb QueryBuilder */
        $qb = $this->getDoctrine()
            ->getManager()
            ->getRepository(Book::class)
            ->createQueryBuilder('b');

        $qb->select('b')
            ->leftJoin('b.authors', 'a');

        foreach ($request->query->all() as $paramName => $paramValue) {
            if (strpos($paramName, 'sort_') === 0) {
                list(, $field) = explode('sort_', $paramName);
                $qb->addOrderBy("b.$field", $paramValue);
            } else if (strpos($paramName, 'search_') === 0) {
                list(, $searchName) = explode('search_', $paramName);
                switch ($searchName) {
                    case 'author':
                        $qb
                            ->where($qb->expr()->orX(
                                $qb->expr()->like('a.firstname', ':authorSearch'),
                                $qb->expr()->like('a.lastname', ':authorSearch')
                            ))
                            ->setParameter('authorSearch', "%$paramValue%");
                        break;
                    default:
                        $qb
                            ->where($qb->expr()->like("b.$searchName", ":{$searchName}Search"))
                            ->setParameter(":{$searchName}Search", "%$paramValue%");
                }
            }
        }

        $aBook = $qb->getQuery()->getResult();

        $return = [];
        /**@var $book \App\Entity\Book */
        foreach ($aBook as $book) {
            $aBookData = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'year' => $book->getYear(),
                'language' => $book->getLanguage(),
                'authors' => array()
            ];

            /**@var \App\Entity\Author */
            foreach ($book->getAuthors() as $author) {
                $aBookData['authors'][] = [
                    'firstname' => $author->getFirstname(),
                    'lastname' => $author->getLastname()
                ];
            }

            $return[] = $aBookData;
        }

        return $this->json($return);
    }
}
