<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Controller\Studio\ShoppingList;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use Pimcore\Bundle\InspireCocktailDemoBundle\MappedParameter\ShoppingListParameters;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\ShoppingListResponse;
use Pimcore\Bundle\InspireCocktailDemoBundle\Service\ShoppingListServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Request\ReferenceRequestBody;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\UserPermissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class CalculateController extends AbstractApiController
{
    private const string ROUTE = '/shopping-list/calculate';

    public function __construct(
        SerializerInterface $serializer,
        private readonly ShoppingListServiceInterface $shoppingListService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_inspire_cocktail_demo_shopping_list_calculate',
        methods: ['POST'],
    )]
    #[Post(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_inspire_cocktail_demo_shopping_list_calculate',
        description: 'bundle_inspire_cocktail_demo_shopping_list_calculate_description',
        summary: 'bundle_inspire_cocktail_demo_shopping_list_calculate_summary',
        tags: [Tags::InspireCocktailDemo->value],
    )]
    #[ReferenceRequestBody(ShoppingListParameters::class)]
    #[SuccessResponse(
        description: 'bundle_inspire_cocktail_demo_shopping_list_calculate_success_response',
        content: new JsonContent(ref: ShoppingListResponse::class, type: 'object'),
    )]
    #[IsGranted(UserPermissions::PIMCORE_USER->value)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
    ])]
    public function calculateShoppingList(
        #[MapRequestPayload] ShoppingListParameters $parameters,
    ): JsonResponse {
        return $this->jsonResponse(
            $this->shoppingListService->calculateShoppingList($parameters),
        );
    }
}
