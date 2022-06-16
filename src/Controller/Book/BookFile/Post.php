<?php


namespace App\Controller\Book\BookFile;


use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\Book\ElectronicBook\File;
use App\Entity\Book\ElectronicBook\Information\ElectronicBookInformation;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class Post extends AbstractCreateFile
{
    public function __construct(
        EntityManagerInterface           $entityManager,
        ValidatorInterface               $validator,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        protected StorageInterface       $storage
    )
    {
        parent::__construct($entityManager, $validator, $resourceMetadataFactory);
    }

    protected function getEntity(): FileInterface
    {
        return new File();
    }

    public function __invoke(Request $request)
    {
        $electronicBookInformationId = $request->request->get('electronic_book_information_id');
        $file = $request->files->get('file');

        if (!is_string($electronicBookInformationId) && !$file instanceof UploadedFile) {
            throw new BadRequestException("One of electronic_book_information_id or file is required");
        }

        if (is_string($electronicBookInformationId) && $file instanceof UploadedFile) {
            throw new BadRequestException("Only one of electronic_book_information_id or file is allowed");
        }

        if (is_string($electronicBookInformationId)) {
            return $this->createFileFromElectronicBookInformation($electronicBookInformationId, $request);
        } else {
            return parent::__invoke($request);
        }
    }

    private function createFileFromElectronicBookInformation(string $electronicBookInformationId, Request $request): FileInterface
    {
        $electronicBookInformation = $this->entityManager->find(ElectronicBookInformation::class, $electronicBookInformationId);

        if (!$electronicBookInformation instanceof ElectronicBookInformation) {
            throw new BadRequestHttpException("Invalid electronic_book_information_id");
        }

        $filePath = $this->storage->resolvePath($electronicBookInformation->getBookFile(), 'file');
        //The class must be UploadedFile and not just File for VichUploadBundle to work as expected. The "test" param
        //must be set to true to disable some verification with is_uploaded_file - I could not find a cleaner way to make
        //it work.
        $electronicBookInformationFile = new UploadedFile($filePath, basename($filePath), test: true);

        $file = $this->getEntity();
        $file->setFile($electronicBookInformationFile);

        $this->validate($file, $request);

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return $file;
    }
}
