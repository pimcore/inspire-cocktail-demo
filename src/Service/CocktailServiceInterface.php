<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Service;

use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\CocktailCollectionParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\UpdateCocktail;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\EnvironmentException;
use Pimcore\Bundle\StudioBackendBundle\Exception\Api\NotFoundException;
use Pimcore\Bundle\StudioBackendBundle\Response\Collection;

/**
 * @internal
 */
interface CocktailServiceInterface
{
    /**
     * @throws NotFoundException
     */
    public function listCocktails(CocktailCollectionParameters $parameters): Collection;

    /**
     * @throws NotFoundException
     */
    public function getCocktail(int $id, string $locale): Cocktail;

    /**
     * @throws NotFoundException
     * @throws EnvironmentException
     */
    public function updateCocktail(int $id, UpdateCocktail $parameters): Cocktail;
}
