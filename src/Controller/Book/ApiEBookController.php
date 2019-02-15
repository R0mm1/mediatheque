<?php

namespace App\Controller\Book;


use App\Controller\AbstractController;
use App\Entity\Book;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiEBookController extends AbstractController
{
    /**
     * @Route("/api/book/{bookId}/ebook", name="api_get_ebook", methods="GET")
     * @param $bookId
     * @return BinaryFileResponse|JsonResponse
     */
    public function getEbook($bookId)
    {
        $bookRepository = $bookRepository = $this->getDoctrine()
            ->getManager()
            ->getRepository(Book::class);

        //Get book
        $book = $bookRepository->find($bookId);
        if (!is_object($book)) {
            return $this->json(['error' => "Ce livre n'existe pas"], 404);
        }

        //Get electronic book
        $ebook = $book->getElectronicBook();
        if (!is_object($ebook)) {
            return $this->json(['error' => "Ce livre n'est pas un livre électronique"], 400);
        }

        try {
            $filepath = $this->get('kernel')->getProjectDir() . '/assets/data/book/ebook/' . $ebook->getFile();
            $file = new File($filepath);
        } catch (FileNotFoundException $exception) {
            return $this->json(['error' => "Le livre électronique a été trouvé mais le fichier est manquant"], 500);
        }

        return $this->file($filepath, $book->getTitle() . '.' . $file->getExtension());
    }
}
