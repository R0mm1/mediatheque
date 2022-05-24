<?php


namespace App\Controller\Book;


use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Controller\Mediatheque\AbstractCreateFile;
use App\Entity\Book\Cover;
use App\Entity\Book\ElectronicBookInformation\Image;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vich\UploaderBundle\Storage\StorageInterface;

class CreateCover extends AbstractCreateFile
{
    const PARAM_IMAGE_ID = 'electronic_book_information_image_id';
    const PARAM_FILE = 'file';

    public function __construct(
        EntityManagerInterface           $entityManager,
        ValidatorInterface               $validator,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        protected string                 $projectDir
    )
    {
        parent::__construct($entityManager, $validator, $resourceMetadataFactory);
    }

    protected function getEntity(): FileInterface
    {
        return new Cover();
    }

    public function __invoke(Request $request)
    {
        $electronicBookInformationImageId = $request->request->get(self::PARAM_IMAGE_ID);
        $file = $request->files->get('file');

        if (!is_string($electronicBookInformationImageId) && !$file instanceof UploadedFile) {
            throw new BadRequestException(sprintf(
                "One of %s or %s is required",
                self::PARAM_IMAGE_ID,
                self::PARAM_FILE
            ));
        }

        if (is_string($electronicBookInformationImageId) && $file instanceof UploadedFile) {
            throw new BadRequestException(sprintf(
                "Only one of %s or %s is allowed",
                self::PARAM_IMAGE_ID,
                self::PARAM_FILE
            ));
        }

        if (is_string($electronicBookInformationImageId)) {
            return $this->createFileFromElectronicBookInformationImage($electronicBookInformationImageId, $request);
        } else {
            return parent::__invoke($request);
        }
    }

    public function createFileFromElectronicBookInformationImage(string $electronicBookInformationImageId, Request $request): FileInterface
    {
        $image = $this->entityManager->find(Image::class, $electronicBookInformationImageId);

        if (!$image instanceof Image) {
            throw new BadRequestHttpException(sprintf("Invalid %s", self::PARAM_IMAGE_ID));
        }

        $filePath = sprintf(
            '%s/%s',
            $this->projectDir,
            $image->getPath()
        );

        //The class must be UploadedFile and not just File for VichUploadBundle to work as expected. The "test" param
        //must be set to true to disable some verification with is_uploaded_file - I could not find a cleaner way to make
        //it work.
        $electronicBookInformationFile = new UploadedFile($filePath, basename($filePath), test: true);

        $fileObject = $this->getEntity();
        $fileObject->setFile($electronicBookInformationFile);

        $this->validate($fileObject, $request);

        $this->entityManager->persist($fileObject);
        $this->entityManager->flush();

        return $fileObject;
    }
}
