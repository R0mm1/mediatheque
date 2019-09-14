<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotationRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
class Notation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"notation", "notation_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups({"notation", "notation_write", "notation_read"})
     */
    private $note;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Groups({"notation"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return float|null
     */
    public function getNote(): ?float
    {
        return $this->note;
    }

    /**
     * @param float $note
     * @return Notation
     */
    public function setNote(float $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Notation
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
