<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoFinderResultsResponse',
    title: 'Bundle Inspire Cocktail Demo Finder Results Response',
    required: ['cocktails'],
    type: 'object'
)]
final class FinderResultsResponse implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(
            description: 'Matching cocktails',
            type: 'array',
            items: new Items(ref: Cocktail::class)
        )]
        private readonly array $cocktails,
    ) {
    }

    public function getCocktails(): array
    {
        return $this->cocktails;
    }
}
