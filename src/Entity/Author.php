<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Filter\Author\FullName;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={"filters"={"App\Filter\Author\Fullname"}})
 * @ApiFilter(SearchFilter::class, properties={"firstname": "partial", "lastname": "partial"})
 * @ApiFilter(OrderFilter::class, properties={"lastname": "ASC", "firstname": "ASC"})
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"book:get", "book:list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"book:get", "book:list"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"book:get", "book:list"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $bearthYear;

    /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $deathYear;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $biography;

    public function getId(): ?int
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

    public function getBearthYear(): ?string
    {
        return $this->bearthYear;
    }

    public function setBearthYear(?string $bearthYear): self
    {
        $this->bearthYear = $bearthYear;

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
