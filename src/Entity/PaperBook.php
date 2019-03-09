<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaperBookRepository")
 */
class PaperBook
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Book", mappedBy="paperBook")
     */
    private $book;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBook()
    {
        return $this->book;
    }

    public function setBook($book): self
    {
        $this->book = $book;

        return $this;
    }
}
