<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter;

use Pimcore\Bundle\StudioBackendBundle\MappedParameter\CollectionParameters;

/**
 * @internal
 */
final readonly class CocktailCollectionParameters extends CollectionParameters
{
    public function __construct(
        int $page = 1,
        int $pageSize = 10,
        private string $locale = 'en',
    ) {
        parent::__construct($page, $pageSize);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
