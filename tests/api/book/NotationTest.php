<?php

namespace App\Tests\api\book;

use App\Entity\Book\Notation;
use App\Entity\Book\PaperBook\Book as PaperBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\User;

class NotationTest extends BookTestCase
{
    protected Notation $notation1;
    protected Notation $notation2;
    protected User $owner2;
    protected PaperBook $book1;

    protected ElectronicBook $book2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->book1 = (new PaperBook())
            ->setTitle("Book 1");
        $this->entityManager->persist($this->book1);

        $this->book2 = (new ElectronicBook())
            ->setTitle("Book 2");
        $this->entityManager->persist($this->book2);

        $this->owner2 = (new User('456'))
            ->setFirstname('Justine')
            ->setLastname('Ptitegoutte');
        $this->entityManager->persist($this->owner2);

        $this->notation1 = (new Notation())
            ->setUser($this->owner)
            ->setBook($this->book1)
            ->setNote(5);
        $this->entityManager->persist($this->notation1);

        $this->notation2 = (new Notation())
            ->setUser($this->owner2)
            ->setBook($this->book2)
            ->setNote(8);
        $this->entityManager->persist($this->notation2);

        $this->entityManager->flush();
    }

    public function testList()
    {
        $this->client->jsonRequest('GET', '/book_notations');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(2, $responseContent);
    }

    public function testListBookIdFilter()
    {
        $this->client->jsonRequest('GET', '/book_notations?book.id=' . $this->book1->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);
        $this->assertEquals(
            [
                "book" => "/paper_books/" . $this->book1->getId(),
                "id" => $this->notation1->getId(),
                "note" => 5,
                "user" => "/users/" . $this->owner->getId()
            ],
            $responseContent[0]
        );
    }

    public function testListUserIdFilter()
    {
        $this->client->jsonRequest('GET', '/book_notations?user.id=' . $this->owner2->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);
        $this->assertEquals(
            [
                "book" => "/electronic_books/" . $this->book2->getId(),
                "id" => $this->notation2->getId(),
                "note" => 8,
                "user" => "/users/" . $this->owner2->getId()
            ],
            $responseContent[0]
        );
    }

    public function testPostNotation()
    {
        $notationData = [
            "book" => "/electronic_books/" . $this->book2->getId(),
            "note" => 4
        ];

        $this->client->jsonRequest('POST', '/book_notations', $notationData);
        self::assertResponseStatusCodeSame(201);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals(
            [
                "id" => $responseContent['id'],
                "user" => "/users/" . $this->owner->getId(),
                ...$notationData
            ],
            $responseContent
        );
    }

    public function testPutUserOwnNotation()
    {
        $newNote = $this->notation1->getNote() + 1;
        $this->client->jsonRequest('PUT', '/book_notations/' . $this->notation1->getId(), ["note" => $newNote]);
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals(
            [
                "book" => "/paper_books/" . $this->book1->getId(),
                "id" => $this->notation1->getId(),
                "note" => $newNote,
                "user" => "/users/" . $this->owner->getId()
            ],
            $responseContent
        );
    }

    public function testPutSomeoneElseNotation()
    {
        $newNote = $this->notation2->getNote() + 1;
        $this->client->jsonRequest('PUT', '/book_notations/' . $this->notation2->getId(), ["note" => $newNote]);
        self::assertResponseStatusCodeSame(403);

        $this->entityManager->refresh($this->notation2);
        $this->assertEquals($newNote - 1, $this->notation2->getNote());
    }

    public function testDeleteUserOwnNotation()
    {
        $this->assertIsInt($this->notation1->getId());
        $this->client->jsonRequest('DELETE', '/book_notations/' . $this->notation1->getId());
        self::assertResponseStatusCodeSame(204);
        $this->assertNull($this->notation1->getId());
    }

    public function testDeleteSomeoneElseNotation()
    {
        $this->assertIsInt($this->notation2->getId());
        $this->client->jsonRequest('DELETE', '/book_notations/' . $this->notation2->getId());
        self::assertResponseStatusCodeSame(403);
        $this->assertIsInt($this->notation2->getId());
    }
}
