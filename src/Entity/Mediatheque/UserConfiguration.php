<?php

namespace App\Entity\Mediatheque;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\DataPersister\Mediatheque\UserConfigurationDataPersister;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            output: false,
            read: false
        ),
        new GetCollection(
            filters: [
                'app.filters.mediatheque.user_configuration.search_filter'
            ]
        ),
        new Post(processor: UserConfigurationDataPersister::class),
        new Put(processor: UserConfigurationDataPersister::class),
        new Delete(processor: UserConfigurationDataPersister::class)
    ],
    normalizationContext: ['groups' => ['user_configuration:get']],
    denormalizationContext: ['groups' => ['user_configuration:set']]
)]
#[ORM\Entity]
#[ORM\Table]
#[ORM\UniqueConstraint(name: 'name_value_user_unique', columns: ['name', 'value', 'user_id'])]
class UserConfiguration
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups(['user_configuration:get'])]
    private ?string $id;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['user_configuration:get', 'user_configuration:set'])]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    private ?User $user;

    #[ORM\Column(type: 'json', nullable: false)]
    #[Groups(['user_configuration:get', 'user_configuration:set'])]
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
