<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Event;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\StudioBackendBundle\Event\AbstractPreResponseEvent;

final class CocktailEvent extends AbstractPreResponseEvent
{
    public const string EVENT_NAME = 'pre_response.inspire_cocktail_demo.cocktail';

    public function __construct(
        private readonly Cocktail $cocktail,
    ) {
        parent::__construct($cocktail);
    }

    public function getCocktail(): Cocktail
    {
        return $this->cocktail;
    }
}
