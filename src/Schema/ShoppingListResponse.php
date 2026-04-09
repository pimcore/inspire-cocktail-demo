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
    schema: 'BundleInspireCocktailDemoShoppingListResponse',
    title: 'Shopping List Response',
    required: ['cocktails', 'ingredients'],
    type: 'object',
)]
final class ShoppingListResponse implements AdditionalAttributesInterface
{
    use AdditionalAttributesTrait;

    /**
     * @param Cocktail[] $cocktails
     * @param CocktailIngredient[] $ingredients
     */
    public function __construct(
        #[Property(
            description: 'List of requested cocktails',
            type: 'array',
            items: new Items(ref: Cocktail::class),
        )]
        private readonly array $cocktails,
        #[Property(
            description: 'Aggregated shopping list of ingredients with total amounts',
            type: 'array',
            items: new Items(ref: CocktailIngredient::class),
        )]
        private readonly array $ingredients,
    ) {
    }

    /**
     * @return Cocktail[]
     */
    public function getCocktails(): array
    {
        return $this->cocktails;
    }

    /**
     * @return CocktailIngredient[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
