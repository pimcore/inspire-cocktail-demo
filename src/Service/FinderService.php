<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Service;

use Pimcore\Bundle\GenericDataIndexBundle\Model\Search\Modifier\Filter\Basic\ExcludeFoldersFilter;
use Pimcore\Bundle\GenericDataIndexBundle\Model\Search\Modifier\Filter\FieldType\MultiSelectFilter;
use Pimcore\Bundle\GenericDataIndexBundle\Service\Search\SearchService\DataObject\DataObjectSearchServiceInterface;
use Pimcore\Bundle\GenericDataIndexBundle\Service\Search\SearchService\SearchProviderInterface;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderOptionsParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderOptionsResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderResultsParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderResultsResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\IngredientResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\OptionItemResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Search\TermsAggregationModifier;
use Pimcore\Model\DataObject\Cocktail;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @internal
 */
final readonly class FinderService implements FinderServiceInterface
{
    private const array ALLOWED_FIELDS = ['strength', 'occasion', 'flavourProfile'];

    private const string ES_FIELD_PATTERN = 'standard_fields.%s.keyword';

    public function __construct(
        private SearchProviderInterface $searchProvider,
        private DataObjectSearchServiceInterface $searchService,
    ) {
    }

    public function getOptions(FinderOptionsParameters $parameters): FinderOptionsResponse
    {
        $field = $parameters->getField();

        if (!in_array($field, self::ALLOWED_FIELDS, true)) {
            throw new UnprocessableEntityHttpException(
                sprintf('Invalid field "%s". Allowed: %s', $field, implode(', ', self::ALLOWED_FIELDS))
            );
        }

        $search = $this->searchProvider->createDataObjectSearch();
        $search->addModifier(new ExcludeFoldersFilter());

        $this->applyFilterParameters($search, $parameters->getStrength(), $parameters->getOccasion(), $parameters->getFlavourProfile(), $field);

        $search->setAggregationsOnly(true);
        $search->addModifier(new TermsAggregationModifier(
            fieldName: sprintf(self::ES_FIELD_PATTERN, $field),
            aggregationName: $field,
        ));

        $result = $this->searchService->search($search);

        $options = [];
        $aggregation = $result->getAggregation($field);

        if ($aggregation !== null) {
            foreach ($aggregation->getBuckets() as $bucket) {
                $options[] = new OptionItemResponse(
                    value: (string) $bucket->getKey(),
                    label: $this->formatLabel((string) $bucket->getKey()),
                    count: $bucket->getDocCount(),
                );
            }
        }

        return new FinderOptionsResponse($options);
    }

    public function getResults(FinderResultsParameters $parameters): FinderResultsResponse
    {
        $search = $this->searchProvider->createDataObjectSearch();
        $search->addModifier(new ExcludeFoldersFilter());

        $this->applyFilterParameters($search, $parameters->getStrength(), $parameters->getOccasion(), $parameters->getFlavourProfile());

        $result = $this->searchService->search($search);

        $cocktails = [];
        foreach ($result->getIds() as $id) {
            $cocktail = Cocktail::getById($id);

            if (!$cocktail instanceof Cocktail) {
                continue;
            }

            $cocktails[] = $this->hydrateCocktail($cocktail);
        }

        return new FinderResultsResponse($cocktails);
    }

    private function applyFilterParameters(
        mixed $search,
        ?string $strength,
        ?string $occasion,
        ?string $flavourProfile,
        ?string $excludeField = null
    ): void {
        $filters = [
            'strength' => $strength,
            'occasion' => $occasion,
            'flavourProfile' => $flavourProfile,
        ];

        foreach ($filters as $field => $value) {
            if ($field === $excludeField || $value === null || $value === '') {
                continue;
            }

            $search->addModifier(new MultiSelectFilter($field, [$value]));
        }
    }

    private function hydrateCocktail(Cocktail $cocktail): CocktailResponse
    {
        $ingredients = [];
        foreach ($cocktail->getIngredients() as $meta) {
            $ingredient = $meta->getObject();

            if ($ingredient === null) {
                continue;
            }

            $ingredients[] = new IngredientResponse(
                name: (string) $ingredient->getName('en'),
                amount: $meta->getAmount() !== null ? (float) $meta->getAmount() : null,
                unit: $ingredient->getUnit() !== '' ? $ingredient->getUnit() : null,
                notes: $meta->getNotes() !== '' ? $meta->getNotes() : null,
            );
        }

        return new CocktailResponse(
            id: (int) $cocktail->getId(),
            name: (string) $cocktail->getName('en'),
            description: $cocktail->getDescription('en'),
            glassType: $cocktail->getGlassType(),
            preparationMethod: $cocktail->getPreparationMethod(),
            strength: $cocktail->getStrength(),
            flavourProfile: $cocktail->getFlavourProfile() ?? [],
            occasion: $cocktail->getOccasion() ?? [],
            ingredients: $ingredients,
        );
    }

    private function formatLabel(string $value): string
    {
        return ucwords(str_replace('_', ' ', $value));
    }
}
