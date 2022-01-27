<?php


namespace App\Controller\Mediatheque;


use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Mediatheque\File;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractCreateFile
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ValidatorInterface $validator,
        protected ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
    }

    public function __invoke(Request $request)
    {
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }

        $fileObject = $this->getEntity();
        $fileObject->setFile($uploadedFile);

        $this->validate($fileObject, $request);

        $this->entityManager->persist($fileObject);
        $this->entityManager->flush();

        return $fileObject;
    }

    private function validate(File $file, Request $request)
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);
        $resourceMetadata = $this->resourceMetadataFactory->create(File::class);
        $validationGroups = $resourceMetadata->getOperationAttribute($attributes, 'validation_groups', null, true);

        $this->validator->validate($file, ['groups' => $validationGroups]);
    }

    abstract protected function getEntity(): FileInterface;
}
