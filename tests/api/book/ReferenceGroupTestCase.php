<?php

namespace App\Tests\api\book;

use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\Book\PaperBook\Book as PaperBook;
use App\Entity\Book\ReferenceGroup;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReferenceGroupTestCase extends WebTestCase
{
    protected User $user;

    protected PaperBook $book1;
    protected ElectronicBook $book2;
    protected AudioBook $book3;

    protected ReferenceGroup $referenceGroup1;
    protected ReferenceGroup $referenceGroup2;

    protected ReferenceGroup\Book $referenceGroupElement1_1;
    protected ReferenceGroup\Book $referenceGroupElement1_2;
    protected ReferenceGroup\Book $referenceGroupElement1_3;
    protected ReferenceGroup\Book $referenceGroupElement2_1;
    protected ReferenceGroup\Book $referenceGroupElement2_2;

    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = self::createClient();

        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->user = (new User('123'))
            ->setFirstname("Romain")
            ->setLastname("Quentel");
        $this->entityManager->persist($this->user);

        $this->book1 = (new PaperBook())
            ->setTitle('My paper book');
        $this->entityManager->persist($this->book1);

        $this->book2 = (new ElectronicBook())
            ->setTitle('My electronic book');
        $this->entityManager->persist($this->book2);

        $this->book3 = (new AudioBook())
            ->setTitle('My audio book');
        $this->entityManager->persist($this->book3);

        $this->referenceGroup1 = (new ReferenceGroup())
            ->setComment('Reference group 1');
        $this->entityManager->persist($this->referenceGroup1);

        $this->referenceGroup2 = (new ReferenceGroup())
            ->setComment('Reference group 2');
        $this->entityManager->persist($this->referenceGroup2);

        $this->referenceGroupElement1_1 = (new ReferenceGroup\Book())
            ->setPosition(0)
            ->setReferenceGroup($this->referenceGroup1)
            ->setBook($this->book1);
        $this->entityManager->persist($this->referenceGroupElement1_1);

        $this->referenceGroupElement1_2 = (new ReferenceGroup\Book())
            ->setPosition(1)
            ->setReferenceGroup($this->referenceGroup1)
            ->setBook($this->book2);
        $this->entityManager->persist($this->referenceGroupElement1_2);

        $this->referenceGroupElement1_3 = (new ReferenceGroup\Book())
            ->setPosition(2)
            ->setReferenceGroup($this->referenceGroup1)
            ->setBook($this->book3);
        $this->entityManager->persist($this->referenceGroupElement1_3);

        $this->referenceGroupElement2_1 = (new ReferenceGroup\Book())
            ->setPosition(0)
            ->setReferenceGroup($this->referenceGroup2)
            ->setBook($this->book2);
        $this->entityManager->persist($this->referenceGroupElement2_1);

        $this->referenceGroupElement2_2 = (new ReferenceGroup\Book())
            ->setPosition(1)
            ->setReferenceGroup($this->referenceGroup2)
            ->setBook($this->book3);
        $this->entityManager->persist($this->referenceGroupElement2_2);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->client->loginUser($this->user);
    }
}
