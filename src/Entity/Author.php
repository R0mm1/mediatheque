<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author extends AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function asArray(array $aFields = null): array
    {
        $aReturn = [];
        foreach (['Id', 'Firstname', 'Lastname', 'BearthYear', 'DeathYear', 'Biography'] as $authorPropery) {
            if (is_null($aFields) || in_array($authorPropery, $aFields)) {
                $aReturn[lcfirst($authorPropery)] = $this->{"get$authorPropery"}();
            }
        }
        return $aReturn;
    }
}
