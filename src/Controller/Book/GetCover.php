<?php


namespace App\Controller\Book;


use App\Entity\Book\Cover;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class GetCover extends Controller
{
    public function __invoke(Request $request, EntityManagerInterface $em, StorageInterface $storage, $id)
    {
        $cover = $em->getRepository(Cover::class)->find($id);
        if (!is_object($cover)) {
            throw new NotFoundHttpException('Not Found');
        }
        $path = $storage->resolvePath($cover, 'file');
        return $this->file($path);
    }
}