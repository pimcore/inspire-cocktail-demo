<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Hydrator;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Model\DataObject\Cocktail as CocktailDataObject;

/**
 * @internal
 */
interface FinderHydratorInterface
{
    public function hydrate(CocktailDataObject $cocktail, string $locale): Cocktail;
}
