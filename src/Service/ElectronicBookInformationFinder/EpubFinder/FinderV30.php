<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Entity\Book\ElectronicBook\Information\Image;
use App\Entity\Book\ElectronicBook\Information\ImageType;

class FinderV30 implements FinderV30Interface
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
        $imageItemCallback = function (\SimpleXMLElement $imageItem, Image $image) {
            $type = ImageType::IMAGE;

            $properties = (string)$imageItem['properties'];
            if (str_contains($properties, 'cover-image')) {
                $type = ImageType::COVER;
            }

            $image->setType($type);

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
