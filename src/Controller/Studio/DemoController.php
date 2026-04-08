<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Controller\Studio;

use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Prefix;
use Pimcore\Bundle\InspireCocktailDemoBundle\OpenApi\Config\Tags;
use Pimcore\Bundle\InspireCocktailDemoBundle\Schema\DemoResponse;
use Pimcore\Bundle\StudioBackendBundle\Controller\AbstractApiController;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\DefaultResponses;
use Pimcore\Bundle\StudioBackendBundle\OpenApi\Attribute\Response\SuccessResponse;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\HttpResponseCodes;
use Pimcore\Bundle\StudioBackendBundle\Util\Constant\UserPermissions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 */
final class DemoController extends AbstractApiController
{
    private const string ROUTE = '/demo';

    public function __construct(
        SerializerInterface $serializer,
    ) {
        parent::__construct($serializer);
    }

    #[Route(path: self::ROUTE, name: 'pimcore_studio_api_inspire_cocktail_demo', methods: ['GET'])]
    #[Get(
        path: Prefix::BUNDLE . self::ROUTE,
        operationId: 'bundle_inspire_cocktail_demo_get_demo',
        description: 'bundle_inspire_cocktail_demo_get_demo_description',
        summary: 'bundle_inspire_cocktail_demo_get_demo_summary',
        tags: [Tags::InspireCocktailDemo->value]
    )]
    #[SuccessResponse(
        description: 'bundle_inspire_cocktail_demo_get_demo_success_response',
        content: new JsonContent(ref: DemoResponse::class, type: 'object')
    )]
    #[IsGranted(UserPermissions::PIMCORE_USER->value)]
    #[DefaultResponses([
        HttpResponseCodes::UNAUTHORIZED,
    ])]
    public function demo(): JsonResponse
    {
        return $this->jsonResponse(new DemoResponse('Inspire Cocktail Demo is working!'));
    }
}
