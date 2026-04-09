<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config;

use OpenApi\Attributes\Tag;

#[Tag(
    name: Tags::CocktailDemo->value,
    description: 'bundle_tag_cocktail_demo_description',
)]
/**
 * @internal
 */
enum Tags: string
{
    case CocktailDemo = 'Bundle Cocktail Demo';
}
