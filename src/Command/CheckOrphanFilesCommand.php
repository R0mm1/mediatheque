<?php

namespace App\Command;

use App\Service\Cleaning\OrphanFilesFinderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckOrphanFilesCommand extends Command
{
    protected static $defaultName = 'mediatheque:files:check-orphans';

    public function __construct(
        private OrphanFilesFinderInterface $orphanFilesFinder
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        foreach ($this->orphanFilesFinder->find() as $mappingName => $orphans) {
            if (!empty($orphans)) {
                $symfonyStyle->section($mappingName);
                $symfonyStyle->write($orphans, true);
            }
        }

        return Command::SUCCESS;
    }
}
