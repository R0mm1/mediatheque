<?php

namespace App\Service\Cleaning;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;

/**
 * This service will find all the files in the asset directory that are supposed to be related to an entity, but are
 * not. It relies on the VichUploadBundle configuration and annotations.
 */
class OrphanFilesFinder implements OrphanFilesFinderInterface
{
    public function __construct(
        private readonly array         $mappings,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function find(): array
    {
        $result = [];

        //Retrieves all entities handling files
        $entitiesPerMappingName = [];
        $reader = new AnnotationReader();
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $metadatum) {
            foreach ($metadatum->getReflectionClass()->getProperties() as $reflectionProperty) {
                $uploadableAnnotation = $reader->getPropertyAnnotation($reflectionProperty, UploadableField::class);
                if (!$uploadableAnnotation instanceof UploadableField) {
                    continue;
                }

                if (!isset($entitiesPerMappingName[$uploadableAnnotation->getMapping()])) {
                    $entitiesPerMappingName[$uploadableAnnotation->getMapping()] = [];
                }

                $entitiesPerMappingName[$uploadableAnnotation->getMapping()][] = [
                    'entity' => $metadatum->getName(),
                    'field' => $uploadableAnnotation->getFileNameProperty()
                ];
            }
        }

        //Check all the mapping find in the entities annotations are also defined in the VichUploadBundle configuration
        if (count(array_diff_key($entitiesPerMappingName, $this->mappings)) > 0) {
            throw new \RuntimeException("A mapping is missing in the VichUploadBundle configuration");
        }

        foreach ($this->mappings as $mappingName => $mapping) {
            $this->checkMapping($mappingName, $mapping);

            if (!is_dir($mapping['upload_destination'])) {
                throw new \RuntimeException(sprintf("Directory %s is missing", $mapping['upload_destination']));
            }

            $files = array_filter(scandir($mapping['upload_destination']), function (string $file) use ($mapping) {
                return is_file($mapping['upload_destination'] . '/' . $file);
            });

            $existingFiles = [];
            foreach ($entitiesPerMappingName[$mappingName] as $entityInfo) {
                $entities = $this->entityManager->getRepository($entityInfo['entity'])->findBy([
                    $entityInfo['field'] => $files
                ]);
                array_walk($entities, function (mixed $value) use ($entityInfo, &$existingFiles) {
                    $existingFiles[] = $value->{'get' . ucfirst($entityInfo['field'])}();
                });
            }
            $result[$mappingName] = array_diff($files, $existingFiles);
        }

        return $result;
    }

    private function checkMapping(mixed $mappingName, mixed $mapping): void
    {
        if (!is_string($mappingName)) {
            throw new \RuntimeException("The name of the mapping is not a string");
        }

        if (!is_array($mapping)) {
            throw new \RuntimeException("The mapping is not an array");
        }

        if (!isset($mapping['upload_destination']) || !is_string($mapping['upload_destination'])) {
            throw new \RuntimeException("The upload_destination is missing in the mapping");
        }
    }
}
