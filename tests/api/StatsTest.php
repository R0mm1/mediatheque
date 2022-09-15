<?php

namespace App\Tests\api;

use App\Entity\Author;
use App\Entity\Book\PaperBook\Book as PaperBook;
use App\Entity\Book\ElectronicBook\Book as ElectronicBook;
use App\Entity\Book\AudioBook\Book as AudioBook;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StatsTest extends WebTestCase
{
    private User $user1;

    private PaperBook $paperBook1;
    private PaperBook $paperBook2;
    private PaperBook $paperBook3;

    private ElectronicBook $electronicBook1;
    private ElectronicBook $electronicBook2;

    private AudioBook $audioBook1;

    private Author $author1;
    private Author $author2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->user1 = (new User('abc'))
            ->setFirstname('Ab')
            ->setLastname('C');
        $this->entityManager->persist($this->user1);

        $this->author1 = (new Author())
            ->setFirstname('Author')
            ->setLastname('1');
        $this->author2 = (new Author())
            ->setFirstname('Author')
            ->setLastname('2');
        $this->entityManager->persist($this->author1);
        $this->entityManager->persist($this->author2);

        $this->paperBook1 = (new PaperBook())
            ->setTitle('Paper book 1')
            ->addAuthor($this->author1)
            ->addAuthor($this->author2);
        $this->paperBook2 = (new PaperBook())
            ->setTitle('Paper book 2')
            ->addAuthor($this->author1);
        $this->paperBook3 = (new PaperBook())
            ->setTitle('Paper book 2')
            ->addAuthor($this->author2);
        $this->entityManager->persist($this->paperBook1);
        $this->entityManager->persist($this->paperBook2);
        $this->entityManager->persist($this->paperBook3);

        $this->electronicBook1 = (new ElectronicBook())
            ->setTitle('Electronic book 1')
            ->addAuthor($this->author1);
        $this->electronicBook2 = (new ElectronicBook())
            ->setTitle('Electronic book 2')
            ->addAuthor($this->author2);
        $this->entityManager->persist($this->electronicBook1);
        $this->entityManager->persist($this->electronicBook2);

        $this->audioBook1 = (new AudioBook())
            ->setTitle('Audio book 1')
            ->addAuthor($this->author1);
        $this->entityManager->persist($this->audioBook1);

        $this->entityManager->flush();

        $this->client->loginUser($this->user1);
    }

    public function testGetStats()
    {
        $this->client->jsonRequest('GET', '/stats?booksCount&authorsCount&authorsBooksDistribution');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey(0, $responseContent);
        $stats = $responseContent[0];
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('stats', $stats);
        $stats = $stats['stats'];
        $this->assertEquals(
            [
                'booksCount' => [
                    'paper' => 3,
                    'electronic' => 2,
                    'audio' => 1,
                    'total' => 6
                ],
                'authorsCount' => 2,
                'authorsBooksDistribution' => [
                    0 => [
                        "id" => $this->author1->getId(),
                        "firstname" => "Author",
                        "lastname" => "1",
                        "booksCount" => 4,
                        "booksCountPercent" => 66.7
                    ],
                    1 => [
                        "id" => $this->author2->getId(),
                        "firstname" => "Author",
                        "lastname" => "2",
                        "booksCount" => 3,
                        "booksCountPercent" => 50
                    ]
                ]
            ],
            $stats
        );
    }
}
