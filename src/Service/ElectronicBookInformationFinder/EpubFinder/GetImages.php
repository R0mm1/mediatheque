<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Entity\Book\ElectronicBookInformation\Image;
use App\Entity\Book\ElectronicBookInformation\ImageType;
use App\Service\ElectronicBookInformationFinder\FileStructureException;
use App\Service\ElectronicBookInformationFinder\SimpleXml;

trait GetImages
{
    use SimpleXml;

    protected function doGetImages(RootFileFinderInterface $rootFileFinder, string $extractedEpubDir, callable $imageItemCallback): array
    {
        $images = [];

        $rootFile = $rootFileFinder->find($extractedEpubDir);
        $rootFilePath = $rootFile->getRelativePath();
        $rootFile = $rootFile->getRootFile();
        $this->applyNamespace($rootFile, 'root');

        $imageItems = $rootFile->xpath('//root:manifest/root:item[starts-with(@media-type,"image/")]');
        if (is_array($imageItems)) {
            foreach ($imageItems as $imageItem) {
                $path = $extractedEpubDir.'/'.$imageItem['href'];
                if(!is_readable($path)){
                    //Path may be relative to the location of the root file, instead of the root of the epub
                    //https://www.w3.org/publishing/epub3/epub-packages.html#sec-item-elem
                    $path = $extractedEpubDir.'/'.pathinfo($rootFilePath)['dirname'].'/'.$imageItem['href'];
                }

                if(!is_readable($path)){
                    throw new FileStructureException(sprintf(
                        "Could not locate in epub image with href %s",
                        $imageItem['href']
                    ));
                }

                $image = (new Image())
                    ->setPath('assets/' . explode('assets/', $path, 2)[1])
                    ->setName($imageItem['id'])
                    ->setType(ImageType::IMAGE);

                $images[] = $imageItemCallback($imageItem, $image);
            }
        }

        return $images;
    }
}
