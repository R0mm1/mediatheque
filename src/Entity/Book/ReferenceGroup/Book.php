<?php

namespace App\Entity\Book\ReferenceGroup;

use App\Entity\Book\ReferenceGroup;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * @ORM\Entity()
 * @ORM\Table(name="reference_group_book")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"referenceGroupBook:get", "referenceGroupBook:list", "referenceGroup:get"})
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="groupMemberships")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"referenceGroupBook:get", "referenceGroupBook:list", "referenceGroupBook:set", "referenceGroup:get"})
     */
    private \App\Entity\Book $book;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Groups({"referenceGroupBook:get", "referenceGroupBook:list", "referenceGroupBook:set", "referenceGroup:get"})
     */
    private int $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book\ReferenceGroup", inversedBy="elements")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"referenceGroupBook:get", "referenceGroupBook:list", "referenceGroupBook:set", "book:get"})
     */
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
