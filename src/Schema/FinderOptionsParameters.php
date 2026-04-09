<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

/**
 * @internal
 */
final readonly class FinderOptionsParameters
{
    public function __construct(
        private ?string $field = null,
        private ?string $strength = null,
        private ?string $occasion = null,
        private ?string $flavourProfile = null,
    ) {
    }

    public function getField(): ?string
    {
        return $this->field;
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
