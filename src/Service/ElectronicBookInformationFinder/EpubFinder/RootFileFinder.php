<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Dto\Service\ElectronicBookInformation\RootFile;
use App\Service\ElectronicBookInformationFinder\FileStructureException;
use App\Service\ElectronicBookInformationFinder\SimpleXml;

class RootFileFinder implements RootFileFinderInterface
{
    use SimpleXml;

    /**
     * @throws FileStructureException
     */
    public function find(string $epubDir): RootFile
    {
        static $rootFiles = [];

        if(!isset($rootFiles[$epubDir])){
            $containerFilePath = $epubDir . '/META-INF/container.xml';
            if (!is_readable($containerFilePath)) {
                throw new FileStructureException("Required META-INF/container.xml file in epub doesn't exist or is no readable");
            }

            $containerFile = simplexml_load_file($containerFilePath);
            if ($containerFile === false) {
                throw new FileStructureException("Could not parse META-INF/container.xml file");
            }

            $containerFile = $this->applyNamespace($containerFile, "content");

            $rootFileData = $containerFile->xpath('//content:rootfile')[0] ?? null;

            if (!$rootFileData instanceof \SimpleXMLElement) {
                throw new FileStructureException("There is no root file defined in the META-INF/container.xml");
            }

            $rootFilePath = (string)$rootFileData['full-path'];
            if (strlen($rootFilePath) === 0) {
                throw new FileStructureException("The full-path of the root file is not defined in the META-INF/container.xml");
            }

            $fullRootFilePath = $epubDir . '/' . $rootFilePath;
            if (!is_readable($fullRootFilePath)) {
                throw new FileStructureException(sprintf(
                    "Root directory at %s does not exist or is not readable",
                    $fullRootFilePath
                ));
            }

            $rootFile = simplexml_load_file($fullRootFilePath);
            if ($rootFile === false) {
                throw new FileStructureException(sprintf(
                    "Could not parse %s file",
                    $fullRootFilePath
                ));
            }

            $rootFiles[$epubDir] = new RootFile(
                $rootFile,
                $rootFilePath
            );
        }

        return $rootFiles[$epubDir];
    }
}
