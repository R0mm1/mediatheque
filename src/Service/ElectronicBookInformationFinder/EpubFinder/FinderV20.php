<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Entity\Book\ElectronicBookInformation\Image;
use App\Entity\Book\ElectronicBookInformation\ImageType;

class FinderV20 implements FinderV20Interface
{
    use GetImages;
    use GetTitle;

    public function __construct(
        protected RootFileFinderInterface $rootFileFinder
    )
    {
    }

    public function getImages(string $extractedEpubDir): array
    {
        $imageItemCallback = function(\SimpleXMLElement $imageItem, Image $image){
            $image->setType((string)$imageItem['id'] === 'cover' ? ImageType::COVER : ImageType::IMAGE);
            return $image;
        };

        return $this->doGetImages(
            $this->rootFileFinder,
            $extractedEpubDir,
            $imageItemCallback
        );
    }

    public function getTitle(string $extractedEpubDir): ?string
    {
        return $this->doGetTitle(
            $this->rootFileFinder,
            $extractedEpubDir
        );
    }
}
