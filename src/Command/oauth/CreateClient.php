<?php


namespace App\Command\oauth;


use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClient extends Command
{
    protected static $defaultName = 'mediatheque:oauth:create-client';

    protected $clientManager;

    public function __construct(ClientManagerInterface $clientManager)
    {
        parent::__construct();

        $this->clientManager = $clientManager;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create a new oauth client.')
            ->setHelp('This command allows you to create a new oauth client with password grant type. It has a 
            similar behaviour than the netive comment from the FOSOAuthServerBundle but it display the output has
            valid JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->clientManager->createClient();
        $client->setAllowedGrantTypes(['password']);
        $this->clientManager->updateClient($client);
        print_r(json_encode([
            'clientId' => $client->getPublicId(),
            'clientSecret' => $client->getSecret()
        ]));
    }
}
