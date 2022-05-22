<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Entity\Book\ElectronicBookInformation\Image;

interface FinderInterface
{
    /**
     * @param string $extractedEpubDir
     * @return Image[]
     */
    public function getImages(string $extractedEpubDir): array;

    public function getTitle(string $extractedEpubDir): ?string;
}
