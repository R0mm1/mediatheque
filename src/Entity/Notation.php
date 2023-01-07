<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotationRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "booknotation" = "App\Entity\Book\Notation"
 * })
 */
class Notation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"notation", "notation_read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="float")
     * @Groups({"notation", "notation_write", "notation_read"})
     */
    private float $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @Groups({"notation", "notation_read"})
     */
    private ?User $user = null;

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
     * @return User|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Notation
     */
    public function setUser(UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
}
