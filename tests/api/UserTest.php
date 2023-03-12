<?php

namespace App\Tests\api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    private User $user1;
    private User $user2;

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
        $this->user2 = (new User('def'))
            ->setFirstname('De')
            ->setLastname('F');
        $this->entityManager->persist($this->user2);

        $this->entityManager->flush();

        $this->client->loginUser($this->user1);
    }

    public function testList()
    {
        $this->client->jsonRequest('GET', '/users');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(2, $responseContent);
    }

    public function testSelf()
    {
        $this->client->jsonRequest('GET', '/self');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertEquals(
            [
                "id" => $this->user1->getId(),
                "firstname" => $this->user1->getFirstname(),
                "lastname" => $this->user1->getLastname(),
            ],
            $responseContent
        );
    }
}
