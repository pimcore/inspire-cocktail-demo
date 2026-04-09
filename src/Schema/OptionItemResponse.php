<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleCocktailDemoOptionItem',
    title: 'Bundle Cocktail Demo Option Item',
    required: ['value', 'label', 'count'],
    type: 'object'
)]
final class OptionItemResponse implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Field value', type: 'string', example: 'medium')]
        private readonly string $value,
        #[Property(description: 'Human-readable label', type: 'string', example: 'Medium')]
        private readonly string $label,
        #[Property(description: 'Number of matching cocktails', type: 'integer', example: 5)]
        private readonly int $count,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
