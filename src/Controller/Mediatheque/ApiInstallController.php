<?php

namespace App\Controller\Mediatheque;


use App\Controller\AbstractController;
use App\Service\MedVar;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use FOS\UserBundle\Model\UserManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiInstallController extends AbstractController
{
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var ClientManagerInterface
     */
    private $clientManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var MedVar
     */
    private $medVar;

    public function __construct(KernelInterface $kernel, ClientManagerInterface $clientManager, UserManager $userManager, MedVar $medVar)
    {
        $this->kernel = $kernel;
        $this->clientManager = $clientManager;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/api/install", name="api_install", methods="GET")
     */
    public function install()
    {
        $varKey = 'install.' . __METHOD__;
        if ($this->medVar->getVar($varKey)) {
            return $this->json([
                'Method already executed'
            ], 403);
        }

        $this->executeMigration();

        $this->oAuthConfiguration();

        $this->medVar->setVar($varKey, true);
        
        return $this->json([
            'follow' => [
                'route' => '/api/install/user',
                'method' => 'POST',
                'params' => [
                    'username', 'password'
                ]
            ]
        ]);
    }

    /**
     * @Route("/api/install/user", name="api_install_add_user", methods="POST")
     */
    public function addUser(Request $request)
    {
        $varKey = 'install.' . __METHOD__;
        if ($this->medVar->getVar($varKey)) {
            return $this->json([
                'Method already executed'
            ], 403);
        }

        $params = $this->getParameters($request);

        $user = $this->userManager->createUser();
        $user->setUsername($params['username']);
        $user->setPlainPassword($params['password']);
        $this->userManager->updateUser($user);

        $this->medVar->setVar($varKey, true);

        return $this->json([]);
    }

    /**
     * @throws \Exception
     */
    private function executeMigration()
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate'
        ]);
        $output = new NullOutput();

        $app->run($input, $output);
    }

    /**
     *
     */
    private function oAuthConfiguration()
    {
        $client = $this->clientManager->createClient();
        $client->setAllowedGrantTypes(['password']);
        $this->clientManager->updateClient($client);

        $jsDirectory = $this->kernel->getProjectDir() . '/public/js/';
        $configFilepath = $jsDirectory . 'config.json';
        $config = file_get_contents($configFilepath);
        if (!$config) {
            copy($jsDirectory . '_config.json', $configFilepath);
            $config = file_get_contents($configFilepath);
        }
        $config = json_decode($config, true);

        $config['auth']['client_id'] = $client->getPublicId();
        $config['auth']['client_secret'] = $client->getSecret();

        file_put_contents($configFilepath, json_encode($config, JSON_PRETTY_PRINT));
    }
}