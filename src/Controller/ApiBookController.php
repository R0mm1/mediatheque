<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiBookController extends AbstractController
{
    /**
     * @Route("/api/book", name="api_list_book", methods="GET")
     */
    public function listBook(Request $request)
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
            $return[] = $book->asArray(['Id', 'Title', 'Year', 'Language'], ['Id', 'Firstname', 'Lastname']);
        }

        return $this->json($return);
    }

    /**
     * @Route("/api/book/{id}", name="api_get_book", methods="GET")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getBook($id)
    {
        $book = $this->getDoctrine()
            ->getManager()
            ->getRepository(Book::class)
            ->find($id);
        if (is_object($book)) {
            return $this->json($book->asArray());
        } else {
            return $this->json([], 404);
        }
    }

    /**
     * @Route("/api/book", name="api_add_book", methods="POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addBook(Request $request)
    {
        $book = new Book();

        foreach ($request->request->all() as $paramName => $paramValue) {
            $setter = 'set' . ucfirst($paramName);
            if (is_callable([$book, $setter])) {
                $book->$setter($paramValue);
            }
        }

        if (empty($book->getTitle())) {
            return $this->json(['error' => 'bad_request'], 400);
        }

        $aAuthor = $request->request->get('authors');
        if (!empty($aAuthor)) {
            $repo = $this->getDoctrine()->getManager()->getRepository(Author::class);
            foreach ($aAuthor as $authorId) {
                $author = $repo->find($authorId);
                if (is_object($author)) {
                    $book->addAuthor($author);
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        return $this->json($book->asArray());
    }

    /**
     * @Route("/api/book/{id}", name="api_delete_book", methods="DELETE")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteBook($id)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository(Book::class)->find($id);

        if (is_object($book)) {
            $em->remove($book);
            $em->flush();
            return $this->json([]);
        } else {
            return $this->json(['error' => 'bad_book'], 404);
        }
    }

    /**
     * @Route("/api/book/{id}", name="api_set_book", methods="PUT")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function setBook(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository(Book::class)->find($id);

        if (!is_object($book)) {
            return $this->json(['error' => 'bad_book'], 404);
        }

        $data = json_decode($request->getContent());

        foreach ($data as $paramName => $paramValue) {
            $setter = 'set' . ucfirst($paramName);
            if (is_callable([$book, $setter])) {
                $book->$setter($paramValue);
            }
        }

        $em->persist($book);
        $em->flush();

        return $this->json($book->asArray());
    }

    /**
     * @Route("/api/book/{bookId}/author/{authorId}", name="api_book_add_author", methods="POST")
     * @param $bookId
     * @param $authorId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addAuthor($bookId, $authorId)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository(Book::class)->find($bookId);
        if (!is_object($book)) {
            return $this->json(['bad_book'], 404);
        }

        $author = $em->getRepository(Author::class)->find($authorId);
        if (!is_object($author)) {
            return $this->json(['bad_author'], 404);
        }

        $book->addAuthor($author);

        $em->persist($book);
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/api/book/{bookId}/author/{authorId}", name="api_book_delete_author", methods="DELETE")
     * @param $bookId
     * @param $authorId
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAuthor($bookId, $authorId)
    {
        $em = $this->getDoctrine()->getManager();

        $book = $em->getRepository(Book::class)->find($bookId);
        if (!is_object($book)) {
            return $this->json(['bad_book'], 404);
        }

        $author = $em->getRepository(Author::class)->find($authorId);
        if (!is_object($author)) {
            return $this->json(['bad_author'], 404);
        }

        $book->removeAuthor($author);

        $em->persist($book);
        $em->flush();

        return $this->json([]);
    }
}
