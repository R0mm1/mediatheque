<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Dto\Service\ElectronicBookInformation\RootFile;

interface RootFileFinderInterface
{
    public function find(string $epubDir): RootFile;
}
