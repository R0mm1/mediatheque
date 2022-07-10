<?php

namespace App\Entity\Mediatheque;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="name_value_user_unique", columns={"name", "value", "user_id"})
 *    }
 * )
 */
class UserConfiguration
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"user_configuration:get"})
     */
    private ?string $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Groups({"user_configuration:get", "user_configuration:set"})
     */
    private ?string $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private ?User $user;

    /**
     * @ORM\Column(type="json", nullable=false)
     * @Groups({"user_configuration:get", "user_configuration:set"})
     */
    private array $value = [];

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return UserConfiguration
     */
    public function setName(?string $name): UserConfiguration
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return UserConfiguration
     */
    public function setUser(?User $user): UserConfiguration
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return array
     */
    public function getValue(): array
    {
        return $this->value;
    }

    /**
     * @param array $value
     * @return UserConfiguration
     */
    public function setValue(array $value): UserConfiguration
    {
        $this->value = $value;
        return $this;
    }
}
