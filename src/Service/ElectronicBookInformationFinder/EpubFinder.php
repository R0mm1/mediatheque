<?php

namespace App\Service\ElectronicBookInformationFinder;

use App\Entity\Book\ElectronicBook\Information\Book;
use App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation;
use App\Service\ElectronicBookInformationFinder\EpubFinder\FinderV20Interface;
use App\Service\ElectronicBookInformationFinder\EpubFinder\FinderV30Interface;
use App\Service\ElectronicBookInformationFinder\EpubFinder\RootFileFinderInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class EpubFinder implements EpubFinderInterface
{
    public function __construct(
        protected StorageInterface $storage,
        protected RootFileFinderInterface $rootFileFinder,
        protected FinderV20Interface $finderV20,
        protected FinderV30Interface $finderV30
    )
    {
    }

    /**
     * @throws UnrecoverableException
     * @throws UnsupportedEpubException
     */
    public function find(Book $book): ElectronicBookInformation
    {
        $path = $this->storage->resolvePath($book, 'file');

        if (!is_string($path)) {
            throw new UnrecoverableException("Missing path in book");
        }

        if (!is_readable($path)) {
            throw new UnrecoverableException(sprintf(
                "Cannot read file at path %s",
                $path
            ));
        }

        $archive = new \ZipArchive();
        if (!$archive->open($path)) {
            throw new UnrecoverableException(sprintf(
                "Cannot extract archive at path %s",
                $path
            ));
        }

        $pathinfo = pathinfo($path);
        $extractDir = sprintf(
            '%s/%s_extracted',
            $pathinfo['dirname'],
            $pathinfo['filename']
        );
        $archive->extractTo($extractDir);
        $archive->close();

        $rootFile = $this->rootFileFinder->find($extractDir)->getRootFile();
        $rootFile = $this->applyNamespace($rootFile, 'root');
        $finder = match ((string)$rootFile['version']){
            '2.0' => $this->finderV20,
            '3.0' => $this->finderV30,
            default => throw new UnsupportedEpubException(sprintf(
                "Version %d not supported",
                $rootFile['version']
            ))
        };

        $electronicBookInformation = new ElectronicBookInformation();
        $electronicBookInformation->setTitle($finder->getTitle($extractDir) ?? '');

        foreach ($finder->getImages($extractDir) as $image) {
            $electronicBookInformation->addImage($image);
        }

        return $electronicBookInformation;
    }

    private function applyNamespace(\SimpleXMLElement $simpleXMLElement, string $namespace): \SimpleXMLElement
    {
        foreach ($simpleXMLElement->getDocNamespaces() as $strPrefix => $strNamespace) {
            if (strlen($strPrefix) === 0) {
                $strPrefix = $namespace;
            }
            $simpleXMLElement->registerXPathNamespace($strPrefix, $strNamespace);
        }
        return $simpleXMLElement;
    }
}
