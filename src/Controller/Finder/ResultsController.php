<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Controller\Finder;

use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Query\TextFieldParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\FinderResultsParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderResultsResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Service\FinderServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class ResultsController extends AbstractApiController
{
    private const string ROUTE = '/finder/results';

    public function __construct(
        SerializerInterface $serializer,
        private readonly FinderServiceInterface $finderService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(path: self::ROUTE, name: 'pimcore_studio_api_cocktail_finder_results', methods: ['GET'])]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_cocktail_demo_finder_results_get',
        description: 'bundle_cocktail_demo_finder_results_get_description',
        summary: 'bundle_cocktail_demo_finder_results_get_summary',
        tags: [Tags::CocktailDemo->value]
    )]
    #[TextFieldParameter(name: 'strength', description: 'Filter by strength', required: false, example: 'medium')]
    #[TextFieldParameter(name: 'occasion', description: 'Filter by occasion', required: false, example: 'party')]
    #[TextFieldParameter(name: 'flavourProfile', description: 'Filter by flavour profile', required: false, example: 'bitter')]
    #[SuccessResponse(
        description: 'bundle_cocktail_demo_finder_results_get_success_description',
        content: new JsonContent(ref: FinderResultsResponse::class)
    )]
    #[DefaultResponses([HttpResponseCodes::UNAUTHORIZED])]
    public function getResults(
        #[MapQueryString] FinderResultsParameters $parameters = new FinderResultsParameters(),
    ): JsonResponse {
        return $this->jsonResponse($this->finderService->getResults($parameters));
    }
}
