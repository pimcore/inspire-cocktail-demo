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
    schema: 'BundleCocktailDemoIngredient',
    title: 'Bundle Cocktail Demo Ingredient',
    required: ['name'],
    type: 'object'
)]
final class IngredientResponse implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Ingredient name', type: 'string', example: 'Gin')]
        private readonly string $name,
        #[Property(description: 'Amount', type: 'number', format: 'float', nullable: true, example: 50.0)]
        private readonly ?float $amount,
        #[Property(description: 'Unit of measurement', type: 'string', nullable: true, example: 'ml')]
        private readonly ?string $unit,
        #[Property(description: 'Preparation notes', type: 'string', nullable: true, example: 'freshly squeezed')]
        private readonly ?string $notes,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}
