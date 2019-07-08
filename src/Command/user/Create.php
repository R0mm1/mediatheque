<?php


namespace App\Command\user;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Create extends Command
{
    protected $userService;

    protected static $defaultName = 'user:create';

    public function __construct(\App\Service\User $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Create a new user.')
            ->setHelp('This command allows you to create a new user of the Mediatheque.');

        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userService->createUser(
            $input->getArgument('username'),
            $input->getArgument('password')
        );
        $user = $this->userService->getLoadedUser();


        $title = 'New user created for the Mediatheque';
        $output->writeln([$title, str_repeat('=', strlen($title))]);

        $output
            ->writeln([
                'Id: ' . $user->getId(),
                'Username: ' . $user->getUsername(),
                'Roles: ' . implode(', ', $user->getRoles())
            ]);

    }
}
