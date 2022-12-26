<?php

namespace App\Controller\Book\ReferenceGroup;

use App\Service\Book\ReferenceGroupSorterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Order
{
    public function __invoke(ReferenceGroupSorterInterface $referenceGroupSorter, Request $request, $id)
    {
        $requestContent = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if(!isset($requestContent['books']) || !is_array($requestContent['books'])){
            throw new BadRequestHttpException("Missing or invalid books param");
        }
        return $referenceGroupSorter->order($id, $requestContent['books']);
    }
}
