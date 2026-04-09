<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config;

use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;

/**
 * @internal
 */
final class Prefix
{
    public const string BUNDLE = AbstractApiController::PREFIX . '/bundle/cocktail-demo';
}
