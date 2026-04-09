<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Service;

use Pimcore\Bundle\InspireCocktailDemoBundle\Event\ShoppingListEvent;
use Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator\CocktailHydratorInterface;
use Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator\ShoppingListHydratorInterface;
use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\ShoppingListParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Repository\CocktailRepositoryInterface;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\ShoppingListResponse;
use Pimcore\Model\DataObject\Cocktail;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Ingredient;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
final readonly class ShoppingListService implements ShoppingListServiceInterface
{
    public function __construct(
        private CocktailRepositoryInterface $cocktailRepository,
        private CocktailHydratorInterface $cocktailHydrator,
        private ShoppingListHydratorInterface $shoppingListHydrator,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function calculateShoppingList(ShoppingListParameters $parameters): ShoppingListResponse
    {
        $locale = $parameters->getLocale();
        $cocktails = [];
        /** @var array<int, array{name: string, amount: float, unit: ?string}> $aggregatedIngredients */
        $aggregatedIngredients = [];

        foreach ($parameters->getItems() as $item) {
            $cocktailDataObject = $this->cocktailRepository->getCocktailById($item->getCocktailId());
            $cocktails[] = $this->cocktailHydrator->hydrate($cocktailDataObject, $locale);

            $this->aggregateIngredients(
                $cocktailDataObject,
                $item->getAmount(),
                $locale,
                $aggregatedIngredients,
            );
        }

        $ingredients = $this->shoppingListHydrator->hydrateAggregatedIngredients($aggregatedIngredients);

        $response = $this->shoppingListHydrator->hydrateShoppingList($cocktails, $ingredients);
        $this->eventDispatcher->dispatch(
            new ShoppingListEvent($response),
            ShoppingListEvent::EVENT_NAME,
        );

        return $response;
    }

    /**
     * @param array<int, array{name: string, amount: float, unit: ?string}> $aggregatedIngredients
     */
    private function aggregateIngredients(
        Cocktail $cocktail,
        int $quantity,
        string $locale,
        array &$aggregatedIngredients,
    ): void {
        foreach ($cocktail->getIngredients() as $relation) {
            if (!$relation instanceof ObjectMetadata) {
                continue;
            }

            $ingredient = $relation->getObject();

            if (!$ingredient instanceof Ingredient) {
                continue;
            }

            /** @var mixed $amount */
            $amount = $relation->getAmount();

            if ($amount === null || $amount === '') {
                continue;
            }

            $ingredientId = $ingredient->getId();
            $scaledAmount = (float) $amount * $quantity;

            if (isset($aggregatedIngredients[$ingredientId])) {
                $aggregatedIngredients[$ingredientId]['amount'] += $scaledAmount;
            } else {
                $aggregatedIngredients[$ingredientId] = [
                    'name' => (string) $ingredient->getName($locale),
                    'amount' => $scaledAmount,
                    'unit' => $ingredient->getUnit(),
                ];
            }
        }
    }
}
