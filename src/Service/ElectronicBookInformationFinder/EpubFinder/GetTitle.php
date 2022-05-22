<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

trait GetTitle
{
    protected function doGetTitle(RootFileFinderInterface $rootFileFinder, string $extractedEpubDir): ?string
    {
        $rootFile = $rootFileFinder->find($extractedEpubDir)->getRootFile();

        return $rootFile?->metadata?->children('dc', true)?->title;
    }
}
