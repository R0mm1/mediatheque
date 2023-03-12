<?php


namespace App\Controller\Mediatheque;


use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Mediatheque\File;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractCreateFile
{
    public function __construct(
        protected EntityManagerInterface                     $entityManager,
        protected ValidatorInterface                         $validator,
        protected ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory
    )
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

        $this->validate($fileObject);

        $this->entityManager->persist($fileObject);
        $this->entityManager->flush();

        return $fileObject;
    }

    protected function validate(File $file): void
    {
        $resourceMetadata = $this->resourceMetadataFactory->create(File::class);

        $it = $resourceMetadata->getIterator();
        while ($it->valid()) {
            $metadata = $it->current();
            foreach ($metadata->getOperations() ?? [] as $operation) {
                if ($operation->getMethod() === 'POST') {
                    $validationGroups = $operation->getValidationContext();
                }
            }
            $it->next();
        }

        if (isset($validationGroups) && isset($validationGroups['groups']) && is_array($validationGroups['groups'])) {
            $this->validator->validate($file, $validationGroups['groups']);
        }
    }

    abstract protected function getEntity(): FileInterface;
}
