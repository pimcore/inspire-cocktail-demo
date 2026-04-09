<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailIngredient;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\ShoppingListResponse;

/**
 * @internal
 */
interface ShoppingListHydratorInterface
{
    /**
     * @param Cocktail[] $cocktails
     * @param CocktailIngredient[] $ingredients
     */
    public function hydrateShoppingList(array $cocktails, array $ingredients): ShoppingListResponse;

    /**
     * @param array<int, array{name: string, amount: float, unit: ?string}> $aggregatedIngredients
     *
     * @return CocktailIngredient[]
     */
    public function hydrateAggregatedIngredients(array $aggregatedIngredients): array;
}
