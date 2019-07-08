<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"book"}, "enable_max_depth"=true},
 *          "denormalization_context"={"groups"={"book"}, "enable_max_depth"=true}
 *     },
 *     itemOperations={"GET", "PUT", "DELETE"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PaperBookRepository")
 */
class PaperBook
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"book"})
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
