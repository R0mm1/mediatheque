<?php

namespace App\Tests\api\book;

use App\Entity\Book\PaperBook\Book as PaperBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\User;

class BookTest extends BookTestCase
{
    protected User $owner2;

    protected PaperBook $book1;
    protected PaperBook $book2;
    protected ElectronicBook $book3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner2 = (new User('456'))
            ->setFirstname('Aida')
            ->setLastname('Bugg');
        $this->entityManager->persist($this->owner2);

        $this->book1 = (new PaperBook())
            ->setTitle('My paper book')
            ->setOwner($this->owner)
            ->addAuthor($this->author1)
            ->addAuthor($this->author2);
        $this->entityManager->persist($this->book1);

        $this->book2 = (new PaperBook())
            ->setTitle('My other paper book')
            ->setOwner($this->owner2)
            ->addAuthor($this->author2);
        $this->entityManager->persist($this->book2);

        $this->book3 = (new ElectronicBook())
            ->setTitle('My electronic book');
        $this->entityManager->persist($this->book3);

        $this->entityManager->flush();
    }

    public function testList()
    {
        $this->client->jsonRequest('GET', '/books');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(3, $responseContent);
    }

    public function testListOwnerFilter()
    {
        $this->client->jsonRequest('GET', '/books?owner=' . $this->owner2->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);
        $this->assertIsArray($responseContent[0]);
        $this->assertArrayHasKey('title', $responseContent[0]);
        $this->assertEquals('My other paper book', $responseContent[0]['title']);
    }

    public function testAuthorFullNameFilter()
    {
        $this->client->jsonRequest('GET', '/books?authorFullname=vic');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);
        $this->assertIsArray($responseContent[0]);
        $this->assertArrayHasKey('title', $responseContent[0]);
        $this->assertEquals('My paper book', $responseContent[0]['title']);
    }

    public function testListBookTypeFilter()
    {
        $this->client->jsonRequest('GET', '/books?bookType=electronic');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);
        $this->assertIsArray($responseContent[0]);
        $this->assertArrayHasKey('title', $responseContent[0]);
        $this->assertEquals('My electronic book', $responseContent[0]['title']);
    }
}
