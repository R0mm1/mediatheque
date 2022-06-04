<?php

namespace App\Entity\Book;

use App\Entity\Book;
use App\Entity\Notation as BaseNotation;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="book_notation")
 */
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
