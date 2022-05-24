<?php

namespace App\Dto\Service\ElectronicBookInformation;

class RootFile
{
    public function __construct(
        private \SimpleXMLElement $rootFile,
        private string            $relativePath
    )
    {
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getRootFile(): \SimpleXMLElement
    {
        return $this->rootFile;
    }

    /**
     * @return string
     */
    public function getRelativePath(): string
    {
        return $this->relativePath;
    }
}
