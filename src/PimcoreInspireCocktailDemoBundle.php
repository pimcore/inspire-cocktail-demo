<?php

declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class PimcoreInspireCocktailDemoBundle extends AbstractPimcoreBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
