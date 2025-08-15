<?php

namespace App\Entity\Book;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\Book\ReferenceGroup\Order;
use App\Entity\Book;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            normalizationContext: ['groups'=>'referenceGroup:list']
        ),
        new Put(),
        new Delete(),
        new Post(),
        new Put(
            uriTemplate: '/reference_groups/{id}/sort',
            controller: Order::class,
            openapiContext: [
                "summary" => "Sort the books in the group by the order they are given in the request body",
                "requestBody" => [
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "books" => [
                                        "type" => "array",
                                        "items" => [
                                            "type" => "string",
                                            "description" => "The ReferenceGroupBookId"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        )
    ],
    normalizationContext: ['groups' =>  ['referenceGroup:get']],
    denormalizationContext: ['groups' =>  ['referenceGroup:set']]
)]
#[ORM\Entity(repositoryClass: \App\Repository\Book\ReferenceGroupRepository::class)]
class ReferenceGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['referenceGroup:get', 'referenceGroup:list', 'book:get', 'referenceGroupBook:get'])]
    private int $id;

    /**
     * @deprecated
     */
    #[ORM\ManyToMany(targetEntity: \App\Entity\Book::class, inversedBy: 'groups')]
    #[ORM\JoinTable(name: 'book_group', joinColumns: [new ORM\JoinColumn(name: 'referenceGroup_id', referencedColumnName: 'id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'book_id', referencedColumnName: 'id')])]
    private Collection $books;

    /**
     * @var Collection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Book\ReferenceGroup\Book::class, mappedBy: 'referenceGroup', cascade: ['remove'])]
    #[Groups(['referenceGroup:get'])]
    private Collection $elements;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['referenceGroup:get', 'referenceGroup:list', 'referenceGroup:set', 'book:get', 'referenceGroupBook:get'])]
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
