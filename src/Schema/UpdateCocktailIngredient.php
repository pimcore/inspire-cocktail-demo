<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoUpdateCocktailIngredient',
    title: 'Update Cocktail Ingredient',
    required: ['ingredientId'],
    type: 'object',
)]
final readonly class UpdateCocktailIngredient
{
    public function __construct(
        #[Property(description: 'ID of the ingredient to link', type: 'integer', example: 1)]
        private int $ingredientId,
        #[Property(
            description: 'Amount of the ingredient',
            type: 'number',
            format: 'float',
            example: 60.0,
            nullable: true,
        )]
        private ?float $amount = null,
        #[Property(
            description: 'Additional notes',
            type: 'string',
            example: 'Fresh squeezed',
            nullable: true,
        )]
        private ?string $notes = null,
    ) {
    }

    public function getIngredientId(): int
    {
        return $this->ingredientId;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}
