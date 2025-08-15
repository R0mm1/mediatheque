<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('migration:author2person')]
class Author2PersonMigrationCommand
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**@var Author[] $authors */
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
