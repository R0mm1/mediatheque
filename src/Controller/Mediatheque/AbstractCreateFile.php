<?php


namespace App\Controller\Mediatheque;


use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Mediatheque\File;
use App\Entity\Mediatheque\FileInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractCreateFile
{

    protected $managerRegistry;
    protected $validator;
    protected $resourceMetadataFactory;

    public function __construct(ManagerRegistry $managerRegistry, ValidatorInterface $validator, ResourceMetadataFactoryInterface $resourceMetadataFactory)
    {
        $this->managerRegistry = $managerRegistry;
        $this->validator = $validator;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
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

        $em = $this->managerRegistry->getManager();
        $em->persist($fileObject);
        $em->flush();

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
