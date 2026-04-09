<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoUpdateCocktail',
    title: 'Update Cocktail',
    type: 'object',
)]
final readonly class UpdateCocktail
{
    /**
     * @param UpdateCocktailIngredient[]|null $ingredients
     */
    public function __construct(
        #[Property(description: 'Locale for localized fields', type: 'string', example: 'en')]
        private string $locale = 'en',
        #[Property(
            description: 'Name of the cocktail',
            type: 'string',
            example: 'Negroni',
            nullable: true,
        )]
        private ?string $name = null,
        #[Property(
            description: 'Description of the cocktail',
            type: 'string',
            example: 'A classic Italian aperitivo',
            nullable: true,
        )]
        private ?string $description = null,
        #[Property(
            description: 'Preparation instructions',
            type: 'string',
            example: 'Stir with ice and strain',
            nullable: true,
        )]
        private ?string $instructions = null,
        #[Property(description: 'Glass type', type: 'string', example: 'rocks', nullable: true)]
        private ?string $glassType = null,
        #[Property(
            description: 'Preparation method',
            type: 'string',
            example: 'stirred',
            nullable: true,
        )]
        private ?string $preparationMethod = null,
        #[Property(
            description: 'Strength level',
            type: 'string',
            example: 'strong',
            nullable: true,
        )]
        private ?string $strength = null,
        #[Property(
            description: 'Flavour profile tags',
            type: 'array',
            items: new Items(type: 'string'),
            example: ['bitter', 'herbal'],
            nullable: true,
        )]
        private ?array $flavourProfile = null,
        #[Property(
            description: 'Occasion tags',
            type: 'array',
            items: new Items(type: 'string'),
            example: ['aperitivo'],
            nullable: true,
        )]
        private ?array $occasion = null,
        #[Property(
            description: 'Ingredients list',
            type: 'array',
            items: new Items(ref: UpdateCocktailIngredient::class),
            nullable: true,
        )]
        private ?array $ingredients = null,
    ) {
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
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

    /**
     * @return string[]|null
     */
    public function getFlavourProfile(): ?array
    {
        return $this->flavourProfile;
    }

    /**
     * @return string[]|null
     */
    public function getOccasion(): ?array
    {
        return $this->occasion;
    }

    /**
     * @return UpdateCocktailIngredient[]|null
     */
    public function getIngredients(): ?array
    {
        return $this->ingredients;
    }
}
