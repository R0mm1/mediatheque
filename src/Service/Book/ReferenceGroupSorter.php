<?php

namespace App\Service\Book;

use App\Entity\Book\ReferenceGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReferenceGroupSorter implements ReferenceGroupSorterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function order(int $referenceGroupId, array $positions): ReferenceGroup
    {
        $referenceGroup = $this->entityManager->find(ReferenceGroup::class, $referenceGroupId);
        if (!$referenceGroup instanceof ReferenceGroup) {
            throw new NotFoundHttpException("Reference group $referenceGroupId not found");
        }

        $referenceGroupBooks = $referenceGroup->getElements()->toArray();
        foreach ($positions as $index => $referenceGroupBookId) {
            //Search for the referenceGroupBook on the collection
            $referenceGroupBook = null;
            $referenceGroupBookIndexInCollection = null;
            foreach ($referenceGroupBooks as $rgbIndex => $rgb) {
                if($rgb->getId() === $referenceGroupBookId){
                    $referenceGroupBook = $rgb;
                    $referenceGroupBookIndexInCollection = $rgbIndex;
                    break;
                }
            }

            if (!$referenceGroupBook instanceof ReferenceGroup\Book) {
                throw new BadRequestHttpException(sprintf(
                    "The reference group book with id %s is missing",
                    $referenceGroupBookId
                ));
            }

            $referenceGroupBook->setPosition($index);
            $this->entityManager->persist($referenceGroupBook);

            //The user may not specify all the referenceGroupBooks of the referenceGroup in the request, so we delete
            //the already positioned rgb to position the remaining later.
            unset($referenceGroupBooks[$referenceGroupBookIndexInCollection]);
        }

        //Every referenceGroupBook not specified by the user in the request are placed at the end
        $remainingIndex = count($positions);
        foreach ($referenceGroupBooks as $referenceGroupBook){
            $referenceGroupBook->setPosition($remainingIndex);
            $this->entityManager->persist($referenceGroupBook);
            $remainingIndex++;
        }

        $this->entityManager->flush();

        return $referenceGroup;
    }
}
