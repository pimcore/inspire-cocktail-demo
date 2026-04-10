<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Controller\Studio\Cocktail;

use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Put;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\Cocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\UpdateCocktail;
use Pimcore\Bundle\InspireCocktailDemoBundle\Service\CocktailServiceInterface;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Parameter\Path\IdParameter;
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
final class UpdateController extends AbstractApiController
{
    private const string ROUTE = '/cocktails/{id}';

    public function __construct(
        SerializerInterface $serializer,
        private readonly CocktailServiceInterface $cocktailService,
    ) {
        parent::__construct($serializer);
    }

    #[Route(
        path: self::ROUTE,
        name: 'pimcore_studio_api_inspire_cocktail_demo_update_cocktail',
        requirements: ['id' => '\d+'],
        methods: ['PUT'],
    )]
    #[Put(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_inspire_cocktail_demo_cocktail_update',
        description: 'bundle_inspire_cocktail_demo_cocktail_update_description',
        summary: 'bundle_inspire_cocktail_demo_cocktail_update_summary',
        tags: [Tags::InspireCocktailDemo->value],
    )]
    #[IdParameter(type: 'cocktail')]
    #[ReferenceRequestBody(UpdateCocktail::class)]
    #[SuccessResponse(
        description: 'bundle_inspire_cocktail_demo_cocktail_update_success_response',
        content: new JsonContent(ref: Cocktail::class, type: 'object'),
    )]
    #[IsGranted(UserPermissions::PIMCORE_USER->value)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
        HttpResponseCodes::NOT_FOUND,
    ])]
    public function updateCocktail(
        int $id,
        #[MapRequestPayload] UpdateCocktail $parameters,
    ): JsonResponse {
        return $this->jsonResponse(
            $this->cocktailService->updateCocktail($id, $parameters),
        );
    }
}
