<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Service;

use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderOptionsParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderOptionsResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderResultsParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderResultsResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @internal
 */
interface FinderServiceInterface
{
    /**
     * @throws UnprocessableEntityHttpException if the requested field is not allowed
     */
    public function getOptions(FinderOptionsParameters $parameters): FinderOptionsResponse;

    public function getResults(FinderResultsParameters $parameters): FinderResultsResponse;
}
