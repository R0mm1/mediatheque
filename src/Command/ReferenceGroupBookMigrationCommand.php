<?php

namespace App\Command;

use App\Entity\Book\ReferenceGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migration:referenceGroupBook')]
class ReferenceGroupBookMigrationCommand
{
    protected static $defaultName = 'migration:referenceGroupBook';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**@var ReferenceGroup[] $groups */
        $groups = $this->entityManager->getRepository(ReferenceGroup::class)->findAll();

        foreach ($groups as $group) {
            $position = 0;
            foreach ($group->getBooks() as $book) {
                $referenceGroupBook = (new ReferenceGroup\Book())
                    ->setBook($book)
                    ->setReferenceGroup($group)
                    ->setPosition($position);
                $this->entityManager->persist($referenceGroupBook);

                $position++;
            }
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
