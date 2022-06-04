<?php

namespace App\Entity\Book\PaperBook;

use App\Entity\Book as BaseBook;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="paper_book")
 */
class Book extends BaseBook
{
}
