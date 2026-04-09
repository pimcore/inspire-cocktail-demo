<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter;

/**
 * @internal
 */
final readonly class FinderResultsParameters
{
    public function __construct(
        private ?string $strength = null,
        private ?string $occasion = null,
        private ?string $flavourProfile = null,
    ) {
    }

    public function getStrength(): ?string
    {
        return $this->strength;
    }

    public function getOccasion(): ?string
    {
        return $this->occasion;
    }

    public function getFlavourProfile(): ?string
    {
        return $this->flavourProfile;
    }
}
