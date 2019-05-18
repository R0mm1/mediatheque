<?php


namespace App\Controller\ElectronicBook;


use App\Entity\ElectronicBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class Get extends Controller
{
    public function __invoke(Request $request, EntityManagerInterface $em, StorageInterface $storage, $id)
    {
        $file = $em->getRepository(ElectronicBook::class)->find($id);
        if (!is_object($file)) {
            throw new NotFoundHttpException('Not Found');
        }
        $path = $storage->resolvePath($file, 'file');
        return $this->file($path);
    }
}