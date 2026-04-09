<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailIngredient;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailListItem;
use Pimcore\Model\DataObject\Cocktail as CocktailDataObject;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Ingredient;

/**
 * @internal
 */
final readonly class CocktailHydrator implements CocktailHydratorInterface
{
    public function hydrate(CocktailDataObject $cocktail, string $locale): Cocktail
    {
        return new Cocktail(
            $cocktail->getId(),
            (string) $cocktail->getName($locale),
            $cocktail->getDescription($locale),
            $cocktail->getGlassType(),
            $cocktail->getPreparationMethod(),
            $cocktail->getStrength(),
            $cocktail->getFlavourProfile() ?? [],
            $cocktail->getOccasion() ?? [],
            $this->hydrateIngredients($cocktail, $locale),
        );
    }

    public function hydrateListItem(CocktailDataObject $cocktail, string $locale): CocktailListItem
    {
        return new CocktailListItem(
            $cocktail->getId(),
            (string) $cocktail->getName($locale),
            $cocktail->getGlassType(),
            $cocktail->getPreparationMethod(),
            $cocktail->getStrength(),
        );
    }

    /**
     * @return CocktailIngredient[]
     */
    private function hydrateIngredients(CocktailDataObject $cocktail, string $locale): array
    {
        $ingredients = [];

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
            /** @var mixed $notes */
            $notes = $relation->getNotes();

            $ingredients[] = new CocktailIngredient(
                $ingredient->getId(),
                (string) $ingredient->getName($locale),
                $amount !== null && $amount !== '' ? (float) $amount : null,
                $notes !== null && $notes !== '' ? (string) $notes : null,
                $ingredient->getUnit(),
            );
        }

        return $ingredients;
    }
}
