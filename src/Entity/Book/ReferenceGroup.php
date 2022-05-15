<?php

namespace App\Entity\Book;

use App\Entity\Book;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;


/**
 * @ApiResource(normalizationContext={"groups"={"referenceGroup"}}, denormalizationContext={"groups"={"referenceGroup"}})
 * @ApiFilter(SearchFilter::class, properties={"id": "exact", "books.id": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"comment": "ASC"})
 * @ORM\Entity(repositoryClass="App\Repository\Book\ReferenceGroupRepository")
 */
class ReferenceGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"referenceGroup"})
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", inversedBy="groups")
     * @ORM\JoinTable(name="book_group",
     *      joinColumns={@ORM\JoinColumn(name="referenceGroup_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="book_id", referencedColumnName="id")})
     * @Groups({"referenceGroup"})
     */
    private $books;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"referenceGroup"})
     */
    private $comment;

    public function __construct()
    {
        $this->books = new ArrayCollection();
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
}
