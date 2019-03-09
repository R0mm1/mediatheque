<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Specification\AndX;
use App\Specification\Like;
use App\Specification\None;
use App\Specification\OrX;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuthorController extends AbstractController
{
    /**
     * @Route("/api/author", name="api_list_author", methods="GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAuthor(Request $request)
    {

        $aSpecification = [];
        $aSort = [];

        foreach ($request->query->all() as $paramName => $paramValue) {
            if (strpos($paramName, 'sort_') === 0) {
                list(, $field) = explode('sort_', $paramName);
                $aSort[$field] = $paramValue;
            } else if (strpos($paramName, 'search_') === 0) {
                list(, $searchName) = explode('search_', $paramName);
                $aSpecification[] = new Like($searchName, $paramValue);
            }
        }

        if (!empty($aSpecification)) {
            $specification = new AndX($aSpecification);
        } else {
            $specification = new None();
        }

        /**@var $authorRepository AuthorRepository */
        $authorRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Author::class);
        $aBook = $authorRepository->match($specification, $aSort);


        $return = [];
        /**@var $book \App\Entity\Author */
        foreach ($aBook as $book) {
            $return[] = $book->asArray(['Id', 'Firstname', 'Lastname']);
        }

        return $this->json($return);
    }

    /**
     * @Route("/api/author/{id}", name="api_get_author", methods="GET", requirements={"id"="\d+"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAuthor($id)
    {
        $author = $this->getDoctrine()
            ->getManager()
            ->getRepository(Author::class)
            ->find($id);

        if (is_object($author)) {
            $aBook = $author->asArray();
            return $this->json($aBook);
        } else {
            return $this->json([], 404);
        }
    }

    /**
     * @Route("/api/author/{id}", name="api_set_author", methods="PUT")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function setBook(Request $request, ValidatorInterface $validator, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /**@var $author Author */
        $author = $em->getRepository(Author::class)->find($id);

        $aError = [];

        if (!is_object($author)) {
            return $this->json(['error' => 'bad_author'], 404);
        }

        $data = $this->getParameters($request);

        foreach ($data as $paramName => $paramValue) {
            $setter = 'set' . ucfirst($paramName);
            if (is_callable([$author, $setter])) {
                $author->$setter($paramValue);
            }
        }

        $aAuthorError = $validator->validate($author);
        if (count($aAuthorError) === 0) {
            $em->persist($author);
            $em->flush();
        } else {
            /**@var $error ConstraintViolationInterface */
            foreach ($aAuthorError as $error) {
                $aError[$error->getPropertyPath()] = $error->getMessage();
            }
        }

        if (empty($aError)) {
            return $this->json($author->asArray());
        } else {
            return $this->json(['errors' => $aError], 400);
        }
    }

    /**
     * @Route("/api/author", name="api_add_author", methods="POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addAuthor(Request $request)
    {
        $author = new Author();

        foreach ($this->getParameters($request) as $paramName => $paramValue) {
            $setter = 'set' . ucfirst($paramName);
            if (is_callable([$author, $setter])) {
                $author->$setter($paramValue);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($author);
        $em->flush();

        return $this->json($author->asArray());
    }

    /**
     * @Route("/api/author/{id}", name="api_delete_author", methods="DELETE")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAuthor($id)
    {
        $em = $this->getDoctrine()->getManager();
        $author = $em->getRepository(Author::class)->find($id);

        if (!is_object($author)) {
            return $this->json(['error' => 'bad_author'], 404);
        }

        $em->remove($author);
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/api/author/search", name="api_author_search", methods="GET")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchAuthor(Request $request)
    {
        $search = $request->query->get('search');

        $specification = new OrX([
            new Like('firstname', $search),
            new Like('lastname', $search)
        ]);

        /**@var $bookRepository AuthorRepository */
        $bookRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Author::class);
        $aAuthor = $bookRepository->match($specification, []);

        $aReturn = [];
        /**@var $author \App\Entity\Author */
        foreach ($aAuthor as $author) {
            $aReturn[] = $author->asArray(['Id', 'Firstname', 'Lastname']);
        }

        return $this->json($aReturn);
    }
}
