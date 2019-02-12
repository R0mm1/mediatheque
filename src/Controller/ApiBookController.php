<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\ElectronicBook;
use App\Entity\PaperBook;
use Doctrine\ORM\QueryBuilder;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiBookController extends AbstractController
{
    /**
     * @Route("/api/book", name="api_list_book", methods="GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listBook(Request $request)
    {
        $aSpecification = [];
        $aSort = [];

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
            $aBook = $book->asArray();
            if (!empty($aBook['picture'])) {
                $aBook['picture'] = 'images/book/' . $aBook['picture'];
            }
            return $this->json($aBook);
        } else {
            return $this->json([], 404);
        }
    }

    /**
     * @Route("/api/book", name="api_add_book", methods="POST")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addBook(Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();

        $book = new Book();

        $parameters = $this->getParameters($request);

        $aError = [];

        //Get and move book cover
        if (!empty($parameters['picture'])) {
            $from = $this->get('kernel')->getProjectDir() . '/public/temp/' . $parameters['picture'];
            $to = $this->get('kernel')->getProjectDir() . '/public/images/book/' . $parameters['picture'];
            rename($from, $to);
        }

        //Set book parameters
        foreach ($this->getParameters($request) as $paramName => $paramValue) {
            $setter = 'set' . ucfirst($paramName);
            if (is_callable([$book, $setter])) {
                $book->$setter($paramValue);
            }
        }

        //Handling specific type of book
        if ($parameters['isElectronic']) {
            $ebook = new ElectronicBook();

            if (!empty($parameters['ebook'])) {
                //Setting file
                $from = $this->get('kernel')->getProjectDir() . '/public/temp/' . $parameters['ebook'];
                $file = new File($from);
                $ebook->changeFile($file);

                //Validating
                $aEbookError = $validator->validate($ebook);
                if (count($aEbookError) === 0) {
                    //If no error, persisting ebook and moving file from temp directory to public directory
                    $em->persist($ebook);
                    $book->setElectronicBook($ebook);

                    $to = $this->get('kernel')->getProjectDir() . '/public/book/ebook/' . $parameters['ebook'];
                    rename($from, $to);
                } else {
                    /**@var $error ConstraintViolationInterface */
                    foreach ($aEbookError as $error) {
                        $property = $error->getPropertyPath();
                        if ($property == 'newFile') {
                            //Set property name to match with the parameter name originally received
                            $property = 'ebook';
                        }
                        $aError[$property] = $error->getMessage();
                    }
                }
            }
        } else {
            $pBook = new PaperBook();
            $em->persist($pBook);
            $book->setPaperBook($pBook);
        }

        //Set authors
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

        $aBookError = $validator->validate($book);
        if (empty($aError) && count($aBookError) === 0) {
            $em->persist($book);
            $em->flush();
        } else {
            /**@var $error ConstraintViolationInterface */
            foreach ($aBookError as $error) {
                $aError[$error->getPropertyPath()] = $error->getMessage();
            }
        }

        if (empty($aError)) {
            return $this->json($book->asArray());
        } else {
            return $this->json(['errors' => $aError], 400);
        }
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

        /**@var $book Book */
        $book = $em->getRepository(Book::class)->find($id);

        if (!is_object($book)) {
            return $this->json(['error' => 'bad_book'], 404);
        }

        $data = $this->getParameters($request);

        foreach ($data as $paramName => $paramValue) {
            $setter = 'set' . ucfirst($paramName);
            if (is_callable([$book, $setter])) {
                $book->$setter($paramValue);
            }
        }

        if (!empty($data['authors'])) {

            /**@var $author Author */
            foreach ($book->getAuthors() as $author) {
                if (!in_array($author->getId(), $data['authors'])) {
                    $book->removeAuthor($author);
                }
            }

            $repo = $this->getDoctrine()->getManager()->getRepository(Author::class);
            foreach ($data['authors'] as $authorId) {
                $author = $repo->find($authorId);
                if (is_object($author)) {
                    $book->addAuthor($author);
                }
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
