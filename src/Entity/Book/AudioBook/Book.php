<?php

namespace App\Entity\Book\AudioBook;

use App\Entity\Book as BaseBook;
use App\Entity\Mediatheque;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

class Book extends BaseBook
{
    /**
     * @var \App\Entity\Mediatheque\File|null
     *
     * @ORM\OneToOne(targetEntity="ElectronicBookFile", cascade={"remove", "persist"}, inversedBy="electronicBook")
     * @Groups({"book:get", "book:set"})
     */
    private ?Mediatheque\File $bookFile;
}
