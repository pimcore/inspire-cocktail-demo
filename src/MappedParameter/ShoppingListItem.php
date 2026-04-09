<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoShoppingListItem',
    title: 'Shopping List Item',
    required: ['cocktailId', 'amount'],
    type: 'object',
)]
final readonly class ShoppingListItem
{
    public function __construct(
        #[Property(description: 'ID of the cocktail', type: 'integer', example: 1)]
        private int $cocktailId,
        #[Property(description: 'Number of cocktails to prepare', type: 'integer', example: 30)]
        private int $amount,
    ) {
    }

    public function getCocktailId(): int
    {
        return $this->cocktailId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
