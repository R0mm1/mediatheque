<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={
 *          "normalization_context"={"groups"={"book:get"}, "enable_max_depth"=true},
 *          "denormalization_context"={"groups"={"book:set"}, "enable_max_depth"=true}
 *     },
 *     itemOperations={"GET", "PUT", "DELETE"}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PaperBookRepository")
 */
class PaperBook extends Book
{
}
