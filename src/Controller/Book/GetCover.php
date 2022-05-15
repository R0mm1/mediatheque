<?php


namespace App\Controller\Book;


use App\Entity\Book\Cover;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class GetCover extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em, StorageInterface $storage, $id)
    {
        $cover = $em->getRepository(Cover::class)->find($id);
        if (!is_object($cover)) {
            throw new NotFoundHttpException('get_cover_entity_not_found');
        }
        $path = $storage->resolvePath($cover, 'file');

        try {
            return $this->file($path);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException('get_cover_file_not_found');
        }
    }
}
