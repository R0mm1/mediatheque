<?php

namespace App\Entity\Book\PaperBook;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Book as BaseBook;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="paper_book")
 */
#[ApiResource(
    shortName: 'PaperBook',
    operations: [
        new Get(),
        new GetCollection(
            normalizationContext: ['groups' => ['book:list']]
        ),
        new Put(),
        new Post()
    ],
    normalizationContext: ['groups' => ['book:get', 'file_read']],
    denormalizationContext: ['groups' => [ 'book:set']]
)]
class Book extends BaseBook
{
}
