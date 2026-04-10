<?php

declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Search;

use Pimcore\Bundle\GenericDataIndexBundle\Model\Search\Modifier\SearchModifierInterface;

final readonly class TermsAggregationModifier implements SearchModifierInterface
{
    public function __construct(
        private string $fieldName,
        private string $aggregationName,
        private int $size = 100,
    ) {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getAggregationName(): string
    {
        return $this->aggregationName;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
