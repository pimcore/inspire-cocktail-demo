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
    schema: 'BundleInspireCocktailDemoCocktailIngredient',
    title: 'Cocktail Ingredient',
    required: ['ingredientId', 'name'],
    type: 'object',
)]
final class CocktailIngredient implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Ingredient ID', type: 'integer', example: 1)]
        private readonly int $ingredientId,
        #[Property(description: 'Ingredient name', type: 'string', example: 'Gin')]
        private readonly string $name,
        #[Property(
            description: 'Amount of the ingredient',
            type: 'number',
            format: 'float',
            example: 60.0,
            nullable: true,
        )]
        private readonly ?float $amount = null,
        #[Property(
            description: 'Additional notes for the ingredient',
            type: 'string',
            example: 'Fresh',
            nullable: true,
        )]
        private readonly ?string $notes = null,
        #[Property(
            description: 'Unit of measurement',
            type: 'string',
            example: 'ml',
            nullable: true,
        )]
        private readonly ?string $unit = null,
    ) {
    }

    public function getIngredientId(): int
    {
        return $this->ingredientId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }
}
