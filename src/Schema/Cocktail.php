<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;
use Pimcore\Bundle\StudioBackendBundle\Util\Schema\AdditionalAttributesInterface;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\AdditionalAttributesTrait;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoCocktail',
    title: 'Cocktail',
    required: ['id', 'name'],
    type: 'object',
)]
final class Cocktail implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Cocktail ID', type: 'integer', example: 1)]
        private readonly int $id,
        #[Property(description: 'Name of the cocktail', type: 'string', example: 'Negroni')]
        private readonly string $name,
        #[Property(
            description: 'Description of the cocktail',
            type: 'string',
            example: 'A classic Italian aperitivo',
            nullable: true,
        )]
        private readonly ?string $description = null,
        #[Property(description: 'Glass type', type: 'string', example: 'rocks', nullable: true)]
        private readonly ?string $glassType = null,
        #[Property(
            description: 'Preparation method',
            type: 'string',
            example: 'stirred',
            nullable: true,
        )]
        private readonly ?string $preparationMethod = null,
        #[Property(
            description: 'Strength level',
            type: 'string',
            example: 'strong',
            nullable: true,
        )]
        private readonly ?string $strength = null,
        #[Property(
            description: 'Flavour profile tags',
            type: 'array',
            items: new Items(type: 'string'),
            example: ['bitter', 'herbal'],
        )]
        private readonly array $flavourProfile = [],
        #[Property(
            description: 'Occasion tags',
            type: 'array',
            items: new Items(type: 'string'),
            example: ['aperitivo'],
        )]
        private readonly array $occasion = [],
        #[Property(
            description: 'Ingredients list',
            type: 'array',
            items: new Items(ref: CocktailIngredient::class),
        )]
        private readonly array $ingredients = [],
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

    public function getDescription(): ?string
    {
        return $this->description;
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

    public function getFlavourProfile(): array
    {
        return $this->flavourProfile;
    }

    public function getOccasion(): array
    {
        return $this->occasion;
    }

    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
