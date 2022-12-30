<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Filter\Person\Fullname;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\UuidV6;

/**
 * @ORM\Entity()
 */
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(
            normalizationContext: ['groups' => ['person:list']],
            filters: [
                Fullname::class,
                'person.search_filter',
                'person.order_filter'
            ]
        ),
        new Post(),
        new Put(),
        new Delete()
    ],
    normalizationContext: ['groups' => ['person:get']],
    denormalizationContext: ['groups' => ['person:set']]
)]
#[ApiFilter(Fullname::class)]
class Person extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"person:get", "person:list", "book:get", "book:list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"person:get", "person:list", "person:set", "book:get", "book:list"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"person:get", "person:list", "person:set", "book:get", "book:list"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     * @Groups({"person:get", "person:set"})
     */
    private $birthYear;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     * @Groups({"person:get", "person:set"})
     */
    private $deathYear;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"person:get", "person:set"})
     */
    private $biography;

    public function getId(): ?UuidV6
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthYear(): ?string
    {
        return $this->birthYear;
    }

    public function setBirthYear(?string $birthYear): self
    {
        $this->birthYear = $birthYear;

        return $this;
    }

    public function getDeathYear(): ?string
    {
        return $this->deathYear;
    }

    public function setDeathYear(?string $deathYear): self
    {
        $this->deathYear = $deathYear;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }
}
