<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Controller\Studio\Cocktail;

use OpenApi\Attributes\Get;
use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\CocktailCollectionParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\CocktailListItem;
use Pimcore\Bundle\InspireCocktailDemoBundle\Service\CocktailServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Query\PageParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Query\PageSizeParameter;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Property\GenericCollection;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\Content\CollectionJson;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\UserPermissions;
use Pimcore\Bundle\StudioBackendBundle\Util\Trait\PaginatedResponseTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class CollectionController extends AbstractApiController
{
    use PaginatedResponseTrait;

    private const string ROUTE = '/cocktails';

    public function __construct(
        SerializerInterface $serializer,
        private readonly CocktailServiceInterface $cocktailService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(path: self::ROUTE, name: 'pimcore_studio_api_inspire_cocktail_demo_list_cocktails', methods: ['GET'])]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_inspire_cocktail_demo_cocktail_collection',
        description: 'bundle_inspire_cocktail_demo_cocktail_collection_description',
        summary: 'bundle_inspire_cocktail_demo_cocktail_collection_summary',
        tags: [Tags::InspireCocktailDemo->value],
    )]
    #[PageParameter]
    #[PageSizeParameter]
    #[SuccessResponse(
        description: 'bundle_inspire_cocktail_demo_cocktail_collection_success_response',
        content: new CollectionJson(new GenericCollection(CocktailListItem::class)),
    )]
    #[IsGranted(UserPermissions::PIMCORE_USER->value)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
    ])]
    public function listCocktails(
        #[MapQueryString] CocktailCollectionParameters $parameters = new CocktailCollectionParameters(),
    ): JsonResponse {
        $collection = $this->cocktailService->listCocktails($parameters);

        return $this->getPaginatedCollection(
            $this->serializer,
            $collection->getItems(),
            $collection->getTotalItems(),
        );
    }
}
