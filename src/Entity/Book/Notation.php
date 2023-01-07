<?php

namespace App\Entity\Book;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\DataPersister\NotationDataPersister;
use App\Entity\Book;
use App\Entity\Notation as BaseNotation;
use App\Filter\Book\Notation\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="book_notation")
 */
#[ApiResource(
    shortName: 'BookNotation',
    operations: [
        new GetCollection(
            filters: ['book_notation.search_filter']
        ),
        new Post(
            processor: NotationDataPersister::class
        ),
        new Delete(
            security: "object.getUser() == user",
            processor: NotationDataPersister::class
        ),
        new Put(
            security: "object.getUser() == user",
            processor: NotationDataPersister::class
        )
    ],
    normalizationContext: ['groups' => ['notation_read']],
    denormalizationContext: ['groups' => [ 'notation_write']]
)]
#[ApiFilter(User::class)]
class Notation extends BaseNotation
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book")
     * @var Book
     * @Groups({"notation", "notation_write", "notation_read"})
     */
    private $book;

    /**
     * @return Book
     */
    public function getBook(): Book
    {
        return $this->book;
    }

    /**
     * @param Book $book
     * @return Notation
     */
    public function setBook(Book $book): self
    {
        $this->book = $book;
        return $this;
    }
}
