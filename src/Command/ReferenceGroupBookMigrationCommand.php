<?php

namespace App\Command;

use App\Entity\Book\ReferenceGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReferenceGroupBookMigrationCommand extends Command
{
    protected static $defaultName = 'migration:referenceGroupBook';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**@var $groups ReferenceGroup[] */
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
