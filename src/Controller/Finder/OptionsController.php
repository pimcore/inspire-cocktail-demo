<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Controller\Finder;

use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Query\TextFieldParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Config\Tags as StudioTags;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\FinderOptionsParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\FinderOptionsResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Service\FinderServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class OptionsController extends AbstractApiController
{
    private const string ROUTE = '/finder/options';

    public function __construct(
        SerializerInterface $serializer,
        private readonly FinderServiceInterface $finderService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(path: self::ROUTE, name: 'pimcore_studio_api_cocktail_finder_options', methods: ['GET'])]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_cocktail_demo_finder_options_get',
        description: 'bundle_cocktail_demo_finder_options_get_description',
        summary: 'bundle_cocktail_demo_finder_options_get_summary',
        tags: [Tags::CocktailDemo->value]
    )]
    #[TextFieldParameter(name: 'field', description: 'The field to aggregate options for', required: true, example: 'strength')]
    #[TextFieldParameter(name: 'strength', description: 'Active strength filter', required: false, example: 'medium')]
    #[TextFieldParameter(name: 'occasion', description: 'Active occasion filter', required: false, example: 'party')]
    #[TextFieldParameter(name: 'flavourProfile', description: 'Active flavour profile filter', required: false, example: 'bitter')]
    #[SuccessResponse(
        description: 'bundle_cocktail_demo_finder_options_get_success_description',
        content: new JsonContent(ref: FinderOptionsResponse::class)
    )]
    #[DefaultResponses([HttpResponseCodes::UNAUTHORIZED, HttpResponseCodes::UNPROCESSABLE_CONTENT])]
    public function getOptions(
        #[MapQueryString] FinderOptionsParameters $parameters = new FinderOptionsParameters(),
    ): JsonResponse {
        return $this->jsonResponse($this->finderService->getOptions($parameters));
    }
}
