<?php

namespace App\Service\ElectronicBookInformationFinder\EpubFinder;

use App\Entity\Book\ElectronicBook\Information\Image;
use App\Entity\Book\ElectronicBook\Information\ImageType;
use App\Service\ElectronicBookInformationFinder\SimpleXml;

class FinderV20 implements FinderV20Interface
{
    use GetImages;
    use GetTitle;
    use SimpleXml;

    public function __construct(
        protected RootFileFinderInterface $rootFileFinder
    )
    {
    }

    public function getImages(string $extractedEpubDir): array
    {
        $coverPath = $this->searchCover($extractedEpubDir);
        $coverPathFromProjectRoot = 'assets/'.explode('assets/', $coverPath, 2)[1];

        $imageItemCallback = function (\SimpleXMLElement $imageItem, Image $image)use($coverPathFromProjectRoot) {
            $image->setType($image->getPath() === $coverPathFromProjectRoot ? ImageType::COVER : ImageType::IMAGE);
            return $image;
        };

        return $this->doGetImages(
            $this->rootFileFinder,
            $extractedEpubDir,
            $imageItemCallback
        );
    }

    protected function searchCover(string $extractedEpubDir): ?string
    {
        //I could not find anything in the epub 2 standard (http://idpf.org/epub/20/spec/OPF_2.0.1_draft.htm) about how
        //to describe which image file is the cover. All the methods above are based on my own experience with epub 2.

        $coverPath = null;

        $rootFileData = $this->rootFileFinder->find($extractedEpubDir);
        $rootFile = $rootFileData->getRootFile();

        $rootFileDirname = pathinfo($rootFileData->getRelativePath())['dirname'];
        if ($rootFileDirname === '.') {
            $rootFileDirname = '';
        }
        $this->applyNamespace($rootFile, 'root');

        //Step 1: The guide section may contain a reference with the type cover, which will point to an (x?)html file.
        //The trick is to search into this (x?)html file for an img tag (or image tag for the fancy ones) and hope it's
        //the cover
        //@see http://idpf.org/epub/20/spec/OPF_2.0.1_draft.htm#Section2.6

        foreach ($rootFile->xpath('root:guide/root:reference[@type="cover"]') as $reference) {
            if (isset($reference['href']) && str_contains((string)$reference['href'], 'html')) {
                $filePath = $extractedEpubDir . '/' . $rootFileDirname . (strlen($rootFileDirname) > 0 ? '/' : '') . $reference['href'];

                //Gets rid of all namespace definitions
                $fileContent = file_get_contents($filePath);
                $fileContent = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $fileContent);
                $fileContent = preg_replace('/[a-zA-Z]+:([a-zA-Z]+[=>])/', '$1', $fileContent);

                $fileParsed = simplexml_load_string($fileContent);
                $images = $fileParsed->xpath('//image');
                if (!is_array($images) || count($images) === 0) {
                    $images = $fileParsed->xpath('//img');
                }

                $cover = null;
                if (is_array($images)) {
                    $cover = array_pop($images);
                }

                if ($cover instanceof \SimpleXMLElement && strlen((string)$cover['href']) > 0) {
                    $coverPath = pathinfo($filePath)['dirname'] . '/' . $cover['href'];
                }
            }
        }

        if (!is_string($coverPath)) {
            //Step 2: If the previous step didn't give result, we can search for a meta tag with attribute "name" set to
            //"cover". The "content" attribute of the tag should match a manifest item id corresponding to the cover image
            //file. There is nothing standard in this, but Calibre seems to be doing this way.

            $meta = $rootFile->xpath('root:metadata/root:meta[@name="cover"]');
            $meta = is_array($meta) ? array_pop($meta) : null;
            if ($meta instanceof \SimpleXMLElement) {
                $item = $rootFile->xpath('root:manifest/root:item[@id="' . $meta['content'] . '"]');
                $item = is_array($item) ? array_pop($item) : null;
                if ($item instanceof \SimpleXMLElement) {
                    $coverPath = $extractedEpubDir . '/' . $rootFileDirname . (strlen($rootFileDirname) > 0 ? '/' : '') . $item['href'];
                }
            }
        }

        return $coverPath;
    }

    public function getTitle(string $extractedEpubDir): ?string
    {
        return $this->doGetTitle(
            $this->rootFileFinder,
            $extractedEpubDir
        );
    }
}
