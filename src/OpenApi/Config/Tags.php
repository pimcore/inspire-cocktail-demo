<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config;

use OpenApi\Attributes\Tag;

#[Tag(
    name: Tags::InspireCocktailDemo->value,
    description: 'bundle_tag_inspire_cocktail_demo_description',
)]
/**
 * @internal
 */
enum Tags: string
{
    case CocktailDemo = 'Bundle Cocktail Demo';
    case InspireCocktailDemo = 'Bundle Inspire Cocktail Demo';
}
