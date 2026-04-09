<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Repository;

use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;
use Pimcore\Model\DataObject\Cocktail;
use Pimcore\Model\DataObject\Data\ObjectMetadata;

/**
 * @internal
 */
interface CocktailRepositoryInterface
{
    /**
     * @return Cocktail[]
     *
     * @throws NotFoundException
     */
    public function getCocktails(int $offset, int $limit): array;

    public function getTotalCount(): int;

    /**
     * @throws NotFoundException
     */
    public function getCocktailById(int $id): Cocktail;

    /**
     * @return ObjectMetadata[]
     */
    public function buildIngredientRelations(array $ingredientData): array;
}
