<?php

namespace App\Entity\Mediatheque;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\DiscriminatorMap(['book_electronicBook_file' => 'App\Entity\Book\ElectronicBook\FileDownloadToken', 'book_audioBook_file' => 'App\Entity\Book\AudioBook\FileDownloadToken'])]
class FileDownloadToken
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Groups(['mediatheque_fileDownloadToken:get'])]
    private ?string $token;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class)]
    private User $user;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?DateTime $created;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): FileDownloadToken
    {
        $this->id = $id;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return FileDownloadToken
     */
    public function setToken(?string $token): FileDownloadToken
    {
        $this->token = $token;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): FileDownloadToken
    {
        $this->user = $user;
        return $this;
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    #[ORM\PrePersist]
    public function created(): void
    {
        $this->created = new DateTime();
    }
}
