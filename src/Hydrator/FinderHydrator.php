<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailIngredient;
use Pimcore\Model\DataObject\Cocktail as CocktailDataObject;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Ingredient;

/**
 * @internal
 */
final readonly class FinderHydrator implements FinderHydratorInterface
{
    public function hydrate(CocktailDataObject $cocktail, string $locale): Cocktail
    {
        return new Cocktail(
            id: (int) $cocktail->getId(),
            name: (string) $cocktail->getName($locale),
            description: $cocktail->getDescription($locale),
            glassType: $cocktail->getGlassType(),
            preparationMethod: $cocktail->getPreparationMethod(),
            strength: $cocktail->getStrength(),
            flavourProfile: $cocktail->getFlavourProfile() ?? [],
            occasion: $cocktail->getOccasion() ?? [],
            ingredients: $this->hydrateIngredients($cocktail, $locale),
        );
    }

    /** @return CocktailIngredient[] */
    private function hydrateIngredients(CocktailDataObject $cocktail, string $locale): array
    {
        $ingredients = [];

        foreach ($cocktail->getIngredients() ?? [] as $relation) {
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
                (int) $ingredient->getId(),
                (string) $ingredient->getName($locale),
                $amount !== null && $amount !== '' ? (float) $amount : null,
                $notes !== null && $notes !== '' ? (string) $notes : null,
                $ingredient->getUnit(),
            );
        }

        return $ingredients;
    }
}
