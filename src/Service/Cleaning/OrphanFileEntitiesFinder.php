<?php

namespace App\Service\Cleaning;

use Symfony\Component\DependencyInjection\ServiceLocator;
use App\Service\Cleaning\OrphanFileEntitiesFinder\OrphanFileFinderInterface;


class OrphanFileEntitiesFinder implements OrphanFileEntitiesFinderInterface
{
    public function __construct(
        private readonly ServiceLocator $orphanFileEntitiesFinders
    )
    {
    }

    public function find(): array
    {
        $result = [];

        /**@var $orphanFileEntitiesFinder OrphanFileFinderInterface*/
        foreach ($this->orphanFileEntitiesFinders->getProvidedServices() as $entityFqdn => $orphanFileEntitiesFinder) {
            $result[$entityFqdn] = $this->orphanFileEntitiesFinders->get($entityFqdn)->find();
        }

        return $result;
    }
}
