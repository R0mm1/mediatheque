<?php


namespace App\Controller\ElectronicBook;


use App\Entity\ElectronicBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class Delete
{
    public function __invoke(Request $request, EntityManagerInterface $em, StorageInterface $storage, $id)
    {
        $file = $em->getRepository(ElectronicBook::class)->find($id);
        if (!is_object($file)) {
            throw new NotFoundHttpException('Not Found');
        }

        $path = $storage->resolvePath($file, 'file');
        unlink($path);

        $em->remove($file);
        $em->flush();

        return new Response('', 204);
    }
}