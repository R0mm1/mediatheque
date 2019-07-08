<?php


namespace App\Command\user;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RenewPassword extends Command
{
    protected $userService;

    protected static $defaultName = 'user:renewPassword';

    public function __construct(\App\Service\User $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Renew user password.')
            ->setHelp('This command allows you to renew the password of a user.');

        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userService->loadUserFromCriteria([
            'username' => $input->getArgument('username')
        ]);
        $this->userService->setPassword($input->getArgument('password'));

        $output->writeln('Password modified');
    }
}
