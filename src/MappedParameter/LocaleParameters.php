<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter;

/**
 * @internal
 */
final readonly class LocaleParameters
{
    public function __construct(
        private string $locale = 'en',
    ) {
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
