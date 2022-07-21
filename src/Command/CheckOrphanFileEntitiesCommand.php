<?php

namespace App\Command;

use App\Service\Cleaning\OrphanFileEntitiesFinder\OrphanFileEntityDto;
use App\Service\Cleaning\OrphanFileEntitiesFinderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CheckOrphanFileEntitiesCommand extends Command
{
    protected static $defaultName = 'mediatheque:files:check-orphan-entities';

    public function __construct(
        private OrphanFileEntitiesFinderInterface $orphanFileEntitiesFinder
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $symfonyStyle = new SymfonyStyle($input, $output);

        foreach ($this->orphanFileEntitiesFinder->find() as $entityFqdn => $orphan) {
            $symfonyStyle->section($entityFqdn);

            $orphansMoreThan24h = array_filter($orphan, function (OrphanFileEntityDto $orphanFileEntityDto) {
                $creationDate = $orphanFileEntityDto->getCreationDate();
                if (!$creationDate instanceof \DateTime) {
                    return false;
                }
                if ((new \DateTime())->getTimestamp() - $creationDate->getTimestamp() > 86400) {
                    return true;
                }
                return false;
            });

            if (count($orphansMoreThan24h) > 0) {
                $symfonyStyle->writeln("Following files are orphans since more than 24h:");
                array_walk($orphansMoreThan24h, function (OrphanFileEntityDto $orphanFileEntityDto) use ($symfonyStyle) {
                    $symfonyStyle->writeln($this->format($orphanFileEntityDto));
                });
            }

            $orphanUnknownSinceWhen = array_filter($orphan, function (OrphanFileEntityDto $orphanFileEntityDto) {
                if (!$orphanFileEntityDto->getCreationDate() instanceof \DateTime) {
                    return true;
                }
                return false;
            });

            if (count($orphanUnknownSinceWhen) > 0) {
                $symfonyStyle->writeln("The following files are orphans but their age cannot be determined because the creation date is missing:");
                array_walk($orphanUnknownSinceWhen, function (OrphanFileEntityDto $orphanFileEntityDto) use ($symfonyStyle) {
                    $symfonyStyle->writeln($this->format($orphanFileEntityDto));
                });
            }
        }
        return Command::SUCCESS;
    }

    private function format(OrphanFileEntityDto $orphanFileEntityDto): string
    {
        return sprintf(
            "<fg=red>#%s %s %s</>",
            str_pad($orphanFileEntityDto->getId(), 4, ' '),
            $orphanFileEntityDto->getCreationDate()->format('Y-m-d H:i:s'),
            $orphanFileEntityDto->getPath()
        );
    }
}
