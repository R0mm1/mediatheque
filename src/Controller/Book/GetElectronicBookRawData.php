<?php


namespace App\Controller\Book;

use App\Entity\ElectronicBook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetElectronicBookRawData extends Controller
{

    public function __invoke(Request $request, EntityManagerInterface $em, $id)
    {
        $file = $em->getRepository(ElectronicBook::class)->find($id);
        if (!is_object($file)) {
            throw new NotFoundHttpException('Not Found');
        }

        return $this->json([
            'id'=>$file->getId(),
            'name'=>basename($file->getPath()),
            'status'=>$file->getStatus()
        ]);
    }
}