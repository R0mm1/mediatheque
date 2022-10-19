<?php

namespace App\Entity\Book;

use App\Entity\Book;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Book\ReferenceGroupRepository")
 */
class ReferenceGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"referenceGroup:get", "referenceGroup:list", "book:get", "referenceGroupBook:get"})
     */
    private int $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="groups")
     * @ORM\JoinTable(name="book_group",
     *      joinColumns={@ORM\JoinColumn(name="referenceGroup_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")})
     * @deprecated
     */
    private Collection $books;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book\ReferenceGroup\Book", mappedBy="referenceGroup", cascade={"remove"})
     * @Groups({"referenceGroup:get"})
     * @var Collection
     */
    private Collection $elements;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"referenceGroup:get", "referenceGroup:list", "referenceGroup:set", "book:get", "referenceGroupBook:get"})
     */
    private string $comment;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     * @return ReferenceGroup
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return mixed
     * @deprecated
     */
    public function getBooks()
    {
        return $this->books;
    }

    public function addBook(Book $book)
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
        }
        return $this;
    }

    public function removeBook(Book $book)
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
        }
        return $this;
    }

    public function setBooks(ArrayCollection $books)
    {
        $this->books = $books;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }
}
