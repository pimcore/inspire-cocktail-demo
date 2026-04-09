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
    schema: 'BundleCocktailDemoCocktail',
    title: 'Bundle Cocktail Demo Cocktail',
    required: ['id', 'name', 'flavourProfile', 'occasion', 'ingredients'],
    type: 'object'
)]
final class CocktailResponse implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    public function __construct(
        #[Property(description: 'Data object ID', type: 'integer', example: 42)]
        private readonly int $id,
        #[Property(description: 'Cocktail name', type: 'string', example: 'Negroni')]
        private readonly string $name,
        #[Property(description: 'Description', type: 'string', nullable: true, example: 'A classic Italian aperitivo')]
        private readonly ?string $description,
        #[Property(description: 'Glass type', type: 'string', nullable: true, example: 'rocks')]
        private readonly ?string $glassType,
        #[Property(description: 'Preparation method', type: 'string', nullable: true, example: 'stirred')]
        private readonly ?string $preparationMethod,
        #[Property(description: 'Alcohol strength', type: 'string', nullable: true, example: 'strong')]
        private readonly ?string $strength,
        #[Property(
            description: 'Flavour profile tags',
            type: 'array',
            items: new Items(type: 'string')
        )]
        private readonly array $flavourProfile,
        #[Property(
            description: 'Suitable occasions',
            type: 'array',
            items: new Items(type: 'string')
        )]
        private readonly array $occasion,
        #[Property(
            description: 'Ingredients list',
            type: 'array',
            items: new Items(ref: IngredientResponse::class)
        )]
        private readonly array $ingredients,
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
