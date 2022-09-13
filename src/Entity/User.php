<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * @ORM\Entity()
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"get_user", "book", "user:list"})
     */
    private string $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $sub;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $last_jit = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"get_user", "book", "user:list"})
     */
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"get_user", "book", "user:list"})
     */
    private ?string $lastname = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="owner")
     */
    private Collection $books;

    public function __construct(string $sub)
    {
        $this->books = new ArrayCollection();
        $this->sub = $sub;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSub(): string
    {
        return $this->sub;
    }

    /**
     * @param string $sub
     */
    public function setSub(string $sub): void
    {
        $this->sub = $sub;
    }

    /**
     * @return string|null
     */
    public function getLastJit(): ?string
    {
        return $this->last_jit;
    }

    /**
     * @param string|null $last_jit
     */
    public function setLastJit(?string $last_jit): void
    {
        $this->last_jit = $last_jit;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setOwner($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getOwner() === $this) {
                $book->setOwner(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getId();
    }
}
