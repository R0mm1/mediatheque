<?php

namespace App\Entity\Book\ReferenceGroup;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Entity\Book\ReferenceGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ApiResource(
    shortName: 'ReferenceGroupBook',
    operations: [
        new Get(),
        new GetCollection(
            normalizationContext: ['groups' => ['referenceGroupBook:list']],
            filters: [
                \App\Filter\Book\ReferenceGroup\Book\ReferenceGroup::class
            ]
        ),
        new Put(),
        new Delete(),
        new Post()
    ],
    normalizationContext: ['groups' => ['referenceGroupBook:get']],
    denormalizationContext: ['groups' => [ 'referenceGroupBook:set']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'reference_group_book')]
class Book
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['referenceGroupBook:get', 'referenceGroupBook:list', 'referenceGroup:get'])]
    private string $id;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Book::class, inversedBy: 'groupMemberships')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['referenceGroupBook:get', 'referenceGroupBook:list', 'referenceGroupBook:set', 'referenceGroup:get'])]
    private \App\Entity\Book $book;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Groups(['referenceGroupBook:get', 'referenceGroupBook:list', 'referenceGroupBook:set', 'referenceGroup:get'])]
    private int $position;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Book\ReferenceGroup::class, inversedBy: 'elements')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['referenceGroupBook:get', 'referenceGroupBook:list', 'referenceGroupBook:set', 'book:get'])]
    private ReferenceGroup $referenceGroup;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return \App\Entity\Book
     */
    public function getBook(): \App\Entity\Book
    {
        return $this->book;
    }

    /**
     * @param \App\Entity\Book $book
     * @return Book
     */
    public function setBook(\App\Entity\Book $book): Book
    {
        $this->book = $book;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return Book
     */
    public function setPosition(int $position): Book
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return ReferenceGroup
     */
    public function getReferenceGroup(): ReferenceGroup
    {
        return $this->referenceGroup;
    }

    /**
     * @param ReferenceGroup $referenceGroup
     * @return Book
     */
    public function setReferenceGroup(ReferenceGroup $referenceGroup): Book
    {
        $this->referenceGroup = $referenceGroup;
        return $this;
    }
}
