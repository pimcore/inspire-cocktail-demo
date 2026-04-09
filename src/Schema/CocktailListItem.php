<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoCocktailListItem',
    title: 'Cocktail List Item',
    required: ['id', 'name'],
    type: 'object',
)]
final class CocktailListItem implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Cocktail ID', type: 'integer', example: 1)]
        private readonly int $id,
        #[Property(description: 'Name of the cocktail', type: 'string', example: 'Negroni')]
        private readonly string $name,
        #[Property(description: 'Glass type', type: 'string', example: 'rocks', nullable: true)]
        private readonly ?string $glassType = null,
        #[Property(description: 'Preparation method', type: 'string', example: 'stirred', nullable: true)]
        private readonly ?string $preparationMethod = null,
        #[Property(description: 'Strength level', type: 'string', example: 'strong', nullable: true)]
        private readonly ?string $strength = null,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGlassType(): ?string
    {
        return $this->glassType;
    }

    public function getPreparationMethod(): ?string
    {
        return $this->preparationMethod;
    }

    public function getStrength(): ?string
    {
        return $this->strength;
    }
}
