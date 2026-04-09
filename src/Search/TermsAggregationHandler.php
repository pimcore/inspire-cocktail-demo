<?php

declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Search;

use Pimcore\Bundle\GenericDataIndexBundle\Attribute\Search\AsSearchModifierHandler;
use Pimcore\Bundle\GenericDataIndexBundle\Model\DefaultSearch\Aggregation\Aggregation;
use Pimcore\Bundle\GenericDataIndexBundle\Model\DefaultSearch\Modifier\SearchModifierContextInterface;

final class TermsAggregationHandler
{
    #[AsSearchModifierHandler]
    public function handleTermsAggregation(
        TermsAggregationModifier $modifier,
        SearchModifierContextInterface $context,
    ): void {
        $context->getSearch()
            ->addAggregation(
                new Aggregation(
                    name: $modifier->getAggregationName(),
                    params: [
                        'terms' => [
                            'field' => $modifier->getFieldName(),
                            'size' => $modifier->getSize(),
                        ],
                    ],
                )
            );
    }
}
