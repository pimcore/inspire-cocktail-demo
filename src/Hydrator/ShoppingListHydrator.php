<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailIngredient;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\ShoppingListResponse;

/**
 * @internal
 */
final readonly class ShoppingListHydrator implements ShoppingListHydratorInterface
{
    /**
     * @param Cocktail[] $cocktails
     * @param CocktailIngredient[] $ingredients
     */
    public function hydrateShoppingList(array $cocktails, array $ingredients): ShoppingListResponse
    {
        return new ShoppingListResponse($cocktails, $ingredients);
    }

    /**
     * @param array<int, array{name: string, amount: float, unit: ?string}> $aggregatedIngredients
     *
     * @return CocktailIngredient[]
     */
    public function hydrateAggregatedIngredients(array $aggregatedIngredients): array
    {
        $ingredients = [];

        foreach ($aggregatedIngredients as $ingredientId => $data) {
            $ingredients[] = new CocktailIngredient(
                $ingredientId,
                $data['name'],
                $data['amount'],
                unit: $data['unit'],
            );
        }

        return $ingredients;
    }
}
