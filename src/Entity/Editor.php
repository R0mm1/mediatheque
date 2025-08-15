<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['editor:get']],
    denormalizationContext: ['groups' => ['editor:set']]
)]
#[ORM\Entity(repositoryClass: \App\Repository\EditorRepository::class)]
class Editor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['editor:get', 'book:get'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['editor:get', 'editor:set', 'book:get'])]
    private ?string $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
