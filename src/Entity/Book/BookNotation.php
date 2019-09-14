<?php

namespace App\Entity\Book;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Book;
use App\Entity\Notation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookNotationRepository")
 */
class BookNotation extends Notation
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
     * @return BookNotation
     */
    public function setBook(Book $book): self
    {
        $this->book = $book;
        return $this;
    }
}
