<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Repository;

use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;
use Pimcore\Model\DataObject\Cocktail;
use Pimcore\Model\DataObject\Cocktail\Listing;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Ingredient;

/**
 * @internal
 */
final readonly class CocktailRepository implements CocktailRepositoryInterface
{
    public function getCocktails(int $offset, int $limit): array
    {
        $listing = new Listing();
        $listing->setOrderKey('key');
        $listing->setOrder('ASC');
        $listing->setOffset($offset);
        $listing->setLimit($limit);

        return $listing->load();
    }

    public function getTotalCount(): int
    {
        $listing = new Listing();

        return $listing->getTotalCount();
    }

    public function getCocktailById(int $id): Cocktail
    {
        $cocktail = Cocktail::getById($id);

        if (!$cocktail instanceof Cocktail) {
            throw new NotFoundException(type: 'Cocktail', id: $id);
        }

        return $cocktail;
    }

    public function buildIngredientRelations(array $ingredientData): array
    {
        $relations = [];

        foreach ($ingredientData as $data) {
            $ingredient = Ingredient::getById($data['ingredientId']);

            if (!$ingredient instanceof Ingredient) {
                continue;
            }

            $meta = new ObjectMetadata(
                'ingredients',
                ['amount', 'notes'],
                $ingredient,
            );
            $meta->setAmount($data['amount'] ?? null);
            $meta->setNotes($data['notes'] ?? null);

            $relations[] = $meta;
        }

        return $relations;
    }
}
