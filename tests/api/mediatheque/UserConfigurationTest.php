<?php

namespace App\Tests\api\mediatheque;

use App\Entity\Mediatheque\UserConfiguration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserConfigurationTest extends WebTestCase
{
    private const USER_1_CONFIGURATION_DATA_1 = [
        "name" => "bookList.columns",
        "value" => [
            "columns" => [
                "Auteur",
                "Titre",
                "Annee"
            ]
        ]
    ];

    private const USER_1_CONFIGURATION_DATA_2 = [
        "name" => "mediatheque.theme",
        "value" => [
            "theme" => "light"
        ]
    ];

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    private User $user1;
    private User $user2;
    private UserConfiguration $user1Configuration1;
    private UserConfiguration $user1Configuration2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->client->disableReboot();

        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->user1 = new User('abc');
        $this->entityManager->persist($this->user1);
        $this->user2 = new User('def');
        $this->entityManager->persist($this->user2);
        $this->user1Configuration1 = (new UserConfiguration())
            ->setName(self::USER_1_CONFIGURATION_DATA_1['name'])
            ->setValue(self::USER_1_CONFIGURATION_DATA_1['value'])
            ->setUser($this->user1);
        $this->entityManager->persist($this->user1Configuration1);
        $this->user1Configuration2 = (new UserConfiguration())
            ->setName(self::USER_1_CONFIGURATION_DATA_2['name'])
            ->setValue(self::USER_1_CONFIGURATION_DATA_2['value'])
            ->setUser($this->user1);
        $this->entityManager->persist($this->user1Configuration2);

        $this->entityManager->flush();
    }

    public function testPostUserConfiguration()
    {
        $userConfiguration = [
            "name" => __METHOD__,
            "value" => [
                "test" => "something"
            ]
        ];
        $this->client->loginUser($this->user1);
        $this->client->jsonRequest('POST', '/user_configurations', $userConfiguration);

        $this->assertResponseStatusCodeSame(201);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('id', $responseContent);
        $this->assertMatchesRegularExpression('~[a-f\d]*~', $responseContent['id']);
        $this->assertUserConfigurationValid($userConfiguration, $responseContent);

        return $responseContent;
    }

    /**
     * Test user1 can list his configuration
     */
    public function testGetSelfUserConfigurations()
    {
        $this->client->loginUser($this->user1);
        $this->client->jsonRequest('GET', '/user_configurations');
        $this->assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(2, $responseContent);

        foreach ($responseContent as $userConfiguration) {
            $this->assertIsArray($userConfiguration);
            $this->assertArrayHasKey('id', $userConfiguration);
            match ($userConfiguration['id']) {
                $this->user1Configuration1->getId() => $this->assertUserConfigurationValid(self::USER_1_CONFIGURATION_DATA_1, $userConfiguration),
                $this->user1Configuration2->getId() => $this->assertUserConfigurationValid(self::USER_1_CONFIGURATION_DATA_2, $userConfiguration),
                default => $this->fail(sprintf(
                    "Unexpected id %s, expected one of [%s,%s]",
                    $userConfiguration['id'],
                    $this->user1Configuration1->getId(),
                    $this->user1Configuration2->getId()
                ))
            };
        }
    }

    /**
     * Test user2 can't see user1's configuration
     */
    public function testGetOthersUserConfigurations()
    {
        $this->client->loginUser($this->user2);
        $this->client->jsonRequest('GET', '/user_configurations');
        $this->assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(0, $responseContent);
    }

    /**
     * Test the item get operation is not available
     */
    public function testGetUserConfiguration()
    {
        $this->client->loginUser($this->user1);
        $this->client->jsonRequest('GET', '/user_configurations/'.$this->user1Configuration1->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test user1 can put his configuration
     */
    public function testPutSelfUserConfiguration()
    {
        $this->client->loginUser($this->user1);
        $newData = self::USER_1_CONFIGURATION_DATA_1;
        $newData['value'] = ['newData' => 'Some new data'];
        $this->client->jsonRequest('PUT', '/user_configurations/'.$this->user1Configuration1->getId(), $newData);
        $this->assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseContent);
        $this->assertEquals($this->user1Configuration1->getId(), $responseContent['id']);
        $this->assertUserConfigurationValid($newData, $responseContent);
    }

    /**
     * Test user2 can't put user1's configuration
     */
    public function testPutOtherUserConfiguration()
    {
        $this->client->loginUser($this->user2);
        $newData = self::USER_1_CONFIGURATION_DATA_1;
        $newData['value'] = ['newData' => 'Some new data'];
        $this->client->jsonRequest('PUT', '/user_configurations/'.$this->user1Configuration1->getId(), $newData);
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test user1 can delete his configuration
     */
    public function testDeleteSelfUserConfiguration()
    {
        $this->client->loginUser($this->user1);
        $this->client->jsonRequest('DELETE', '/user_configurations/'.$this->user1Configuration1->getId());
        $this->assertResponseStatusCodeSame(204);
    }

    /**
     * Test user2 can't delete user1's configuration
     */
    public function testDeleteOtherUserConfiguration()
    {
        $this->client->loginUser($this->user2);
        $this->client->jsonRequest('DELETE', '/user_configurations/'.$this->user1Configuration1->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * Test filter on name
     */
    public function testFilterName()
    {
        $this->client->loginUser($this->user1);
        $this->client->jsonRequest('GET', '/user_configurations?name='.$this->user1Configuration1->getName());
        $this->assertResponseStatusCodeSame(200);
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertCount(1, $responseContent);
    }

    private function assertUserConfigurationValid(array $expected, array $actual)
    {
        $this->assertArrayHasKey('name', $actual);
        $this->assertEquals($expected['name'], $actual['name']);
        $this->assertArrayHasKey('value', $actual);
        $this->assertEquals($expected['value'], $actual['value']);
    }
}
