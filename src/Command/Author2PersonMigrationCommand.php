<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Author2PersonMigrationCommand extends Command
{
    protected static $defaultName = 'migration:author2person';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**@var $authors Author[] */
        $authors = $this->entityManager->getRepository(Author::class)->findAll();

        foreach ($authors as $author) {
            $person = (new Person())
                ->setFirstname($author->getFirstname())
                ->setLastname($author->getLastname())
                ->setBirthYear($author->getBearthYear())
                ->setDeathYear($author->getDeathYear())
                ->setBiography($author->getBiography());
            $this->entityManager->persist($person);
            $author->setPerson($person);
            $this->entityManager->persist($author);
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
