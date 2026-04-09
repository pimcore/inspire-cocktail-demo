<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter;

use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    schema: 'BundleInspireCocktailDemoShoppingListParameters',
    title: 'Shopping List Parameters',
    required: ['items'],
    type: 'object',
)]
final readonly class ShoppingListParameters
{
    /**
     * @param ShoppingListItem[] $items
     */
    public function __construct(
        #[Property(
            description: 'List of cocktails with amounts',
            type: 'array',
            items: new Items(ref: ShoppingListItem::class),
        )]
        private array $items,
        #[Property(description: 'Locale for translations', type: 'string', example: 'en')]
        private string $locale = 'en',
    ) {
    }

    /**
     * @return ShoppingListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
