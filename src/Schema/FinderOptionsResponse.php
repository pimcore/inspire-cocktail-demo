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
    schema: 'BundleCocktailDemoFinderOptionsResponse',
    title: 'Bundle Cocktail Demo Finder Options Response',
    required: ['options'],
    type: 'object'
)]
final class FinderOptionsResponse implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(
            description: 'Available options for the requested field',
            type: 'array',
            items: new Items(ref: OptionItemResponse::class)
        )]
        private readonly array $options,
    ) {
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
