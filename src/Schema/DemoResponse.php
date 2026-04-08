<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Schema;

use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

/**
 * @internal
 */
#[Schema(
    title: 'DemoResponse',
    required: ['message'],
    type: 'object',
)]
final readonly class DemoResponse
{
    public function __construct(
        #[Property(
            description: 'Demo response message',
            type: 'string',
            example: 'Inspire Cocktail Demo is working!',
        )]
        private string $message,
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
