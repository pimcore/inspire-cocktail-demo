<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Event;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailListItem;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

final class CocktailListItemEvent extends AbstractPreResponseEvent
{
    public const string EVENT_NAME = 'pre_response.inspire_cocktail_demo.cocktail_list_item';

    public function __construct(
        private readonly CocktailListItem $cocktailListItem,
    ) {
        parent::__construct($cocktailListItem);
    }

    public function getCocktailListItem(): CocktailListItem
    {
        return $this->cocktailListItem;
    }
}
