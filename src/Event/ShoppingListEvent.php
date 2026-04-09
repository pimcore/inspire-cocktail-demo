<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Event;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\ShoppingListResponse;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

final class ShoppingListEvent extends AbstractPreResponseEvent
{
    public const string EVENT_NAME = 'pre_response.inspire_cocktail_demo.shopping_list';

    public function __construct(
        private readonly ShoppingListResponse $shoppingList,
    ) {
        parent::__construct($shoppingList);
    }

    public function getShoppingList(): ShoppingListResponse
    {
        return $this->shoppingList;
    }
}
