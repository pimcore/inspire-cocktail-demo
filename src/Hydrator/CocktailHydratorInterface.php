<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailListItem;
use Pimcore\Model\DataObject\Cocktail as CocktailDataObject;

/**
 * @internal
 */
interface CocktailHydratorInterface
{
    public function hydrate(CocktailDataObject $cocktail, string $locale): Cocktail;

    public function hydrateListItem(CocktailDataObject $cocktail, string $locale): CocktailListItem;
}
