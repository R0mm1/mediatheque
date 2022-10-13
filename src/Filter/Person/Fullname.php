<?php


namespace App\Filter\Person;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Person;
use Doctrine\ORM\QueryBuilder;

class Fullname extends AbstractFilter
{
    /**
     * Passes a property through the filter.
     */
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'fullname' || $resourceClass !== Person::class) return;

        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like("o.firstname", ':personSearchValue'),
                $queryBuilder->expr()->like("o.lastname", ':personSearchValue')
            ))
            ->setParameter('personSearchValue', "%$value%");
    }

    /**
     * Gets the description of this filter for the given resource.
     *
     * Returns an array with the filter parameter names as keys and array with the following data as values:
     *   - property: the property where the filter is applied
     *   - type: the type of the filter
     *   - required: if this filter is required
     *   - strategy: the used strategy
     *   - is_collection (optional): is this filter is collection
     *   - swagger (optional): additional parameters for the path operation,
     *     e.g. 'swagger' => [
     *       'description' => 'My Description',
     *       'name' => 'My Name',
     *       'type' => 'integer',
     *     ]
     *   - openapi (optional): additional parameters for the path operation in the version 3 spec,
     *     e.g. 'openapi' => [
     *       'description' => 'My Description',
     *       'name' => 'My Name',
     *       'schema' => [
     *          'type' => 'integer',
     *       ]
     *     ]
     * The description can contain additional data specific to a filter.
     *
     * @see \ApiPlatform\Core\Swagger\Serializer\DocumentationNormalizer::getFiltersParameters
     */
    public function getDescription(string $resourceClass): array
    {
        return [
            'fullname' => [
                'property' => 'fullname',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => "Make a search on both the firstname and the lastname"
                ]
            ]
        ];
    }
}
