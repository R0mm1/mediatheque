<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @Assert\Expression(
     *     "is_null(value) or value instanceof \App\Entity\User",
     *     message="L'utilisateur est invalide"
     * )
     * @ORM\ManyToOne(targetEntity="User", inversedBy="books", cascade={"persist", "remove", "merge"})
     * @ORM\JoinColumns({
     *  @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * })
     */
    private $owner;

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

    /**
     * @return null|User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param $owner User|null
     * @return PaperBook
     */
    public function setOwner($owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
