<?php

namespace App\Tests\api;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonTest extends WebTestCase
{
    protected Person $person1;
    protected Person $person2;

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

        $this->user = (new User('123'))
            ->setFirstname("Romain")
            ->setLastname("Quentel");
        $this->entityManager->persist($this->user);

        $this->entityManager->flush();

        $this->client->loginUser($this->user);
    }

    public function testList()
    {
        $this->client->jsonRequest('GET', '/people');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(2, $responseContent);
    }

    public function testListFullnameFilter(){
        $this->client->jsonRequest('GET', '/people?fullname=emi');
        self::assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);

        $this->assertCount(1, $responseContent);

        $this->assertEquals(
            [
                'id' => $this->person2->getId(),
                'firstname' => $this->person2->getFirstname(),
                'lastname' => $this->person2->getLastname()
            ],
            $responseContent[0]
        );
    }
}
