<?php

namespace App\Controller;

use App\Entity\Author;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiAuthorController extends AbstractController
{
    /**
     * @Route("/api/author", name="api_add_author", methods="POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addAuthor(Request $request)
    {
        $author = new Author();

        foreach ($request->request->all() as $paramName => $paramValue) {
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
        /**@var $qb QueryBuilder */
        $qb = $this->getDoctrine()
            ->getManager()
            ->getRepository(Author::class)
            ->createQueryBuilder('a');

        $qb->select('a')
            ->where($qb->expr()->orX(
                $qb->expr()->like('a.firstname', ':authorSearch'),
                $qb->expr()->like('a.lastname', ':authorSearch')
            ))
            ->setParameter(':authorSearch', '%' . $request->query->get('search') . '%');

        $aAuthor = $qb->getQuery()->getResult();

        $aReturn = [];
        /**@var $author \App\Entity\Author */
        foreach ($aAuthor as $author) {
            $aReturn[] = $author->asArray(['Id', 'Firstname', 'Lastname']);
        }

        return $this->json($aReturn);
    }
}
