<?php

namespace App\Tests\api;

use App\Entity\Author;
use App\Entity\Person;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorTest extends WebTestCase
{
    protected Person $person1;
    protected Person $person2;

    protected Author $author1;
    protected Author $author2;

    protected User $user;

    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();

        $container = static::getContainer();
        /**@var $entityManager EntityManagerInterface */
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->person1 = (new Person())
            ->setLastname("Hugo")
            ->setFirstname("Victor");
        $this->entityManager->persist($this->person1);
        $this->person2 = (new Person())
            ->setLastname("Zola")
            ->setFirstname("Émile");
        $this->entityManager->persist($this->person2);

        $this->author1 = (new Author())
            ->setPerson($this->person1);
        $this->entityManager->persist($this->author1);
        $this->author2 = (new Author())
            ->setPerson($this->person2);
        $this->entityManager->persist($this->author2);

        $this->user = (new User('123'))
            ->setFirstname("Romain")
            ->setLastname("Quentel");
        $this->entityManager->persist($this->user);

        $this->entityManager->flush();

        $this->client->loginUser($this->user);
    }

    public function testList()
    {
        $this->client->jsonRequest('GET', '/authors');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(2, $responseContent);
    }

    public function testListPersonFilter()
    {
        $this->client->jsonRequest('GET', '/authors?person=' . $this->person1->getId());
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);

        $this->assertEquals(
            [
                'id' => $this->author1->getId(),
                'person' => [
                    'id' => $this->person1->getId(),
                    'firstname' => $this->person1->getFirstname(),
                    'lastname' => $this->person1->getLastname()
                ]
            ],
            $responseContent[0]
        );
    }

    public function testListFullnameFilter()
    {
        $this->client->jsonRequest('GET', '/authors?fullname=Emi');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);

        $this->assertEquals(
            [
                'id' => $this->author2->getId(),
                'person' => [
                    'id' => $this->person2->getId(),
                    'firstname' => $this->person2->getFirstname(),
                    'lastname' => $this->person2->getLastname()
                ]
            ],
            $responseContent[0]
        );
    }

    public function testPostAuthor()
    {
        $authorData = [
            'person' => [
                'firstname' => 'Émile',
                'lastname' => 'Zola'
            ]
        ];

        $this->client->jsonRequest('POST', '/authors', $authorData);

        self::assertResponseStatusCodeSame(201);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey('id', $responseContent);
        $this->assertMatchesRegularExpression('#\d*#', $responseContent['id']);
        $this->assertArrayHasKey('person', $responseContent);
        $this->assertIsArray($responseContent['person']);
        $this->assertArrayHasKey('id', $responseContent['person']);
        $this->assertMatchesRegularExpression('#[a-f\d-]*#', $responseContent['person']['id']);
        $this->assertArrayHasKey('firstname', $responseContent['person']);
        $this->assertEquals('Émile', $responseContent['person']['firstname']);
        $this->assertArrayHasKey('lastname', $responseContent['person']);
        $this->assertEquals('Zola', $responseContent['person']['lastname']);
    }

    public function testPutAuthor()
    {
        $modifiedFirstname = $this->author1->getPerson()->getFirstname() . '_modified';
        $modifiedLastname = $this->author1->getPerson()->getLastname() . '_modified';
        $authorData = [
            '@id' => $this->author1->getId(),
            'person' => [
                '@id' => $this->author1->getPerson()->getId(),
                'firstname' => $modifiedFirstname,
                'lastname' => $modifiedLastname,
            ]
        ];

        $this->client->jsonRequest('PUT', '/authors/'.$this->author1->getId(), $authorData);

        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertArrayHasKey('id', $responseContent);
        $this->assertEquals($this->author1->getId(), $responseContent['id']);
        $this->assertArrayHasKey('person', $responseContent);
        $this->assertIsArray($responseContent['person']);
        $this->assertArrayHasKey('id', $responseContent['person']);
        $this->assertEquals($this->author1->getPerson()->getId(), $responseContent['person']['id']);
        $this->assertArrayHasKey('firstname', $responseContent['person']);
        $this->assertEquals($modifiedFirstname, $responseContent['person']['firstname']);
        $this->assertArrayHasKey('lastname', $responseContent['person']);
        $this->assertEquals($modifiedLastname, $responseContent['person']['lastname']);
    }
}
