<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Service;

use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\ShoppingListParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\ShoppingListResponse;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;

/**
 * @internal
 */
interface ShoppingListServiceInterface
{
    /**
     * @throws NotFoundException
     */
    public function calculateShoppingList(ShoppingListParameters $parameters): ShoppingListResponse;
}
