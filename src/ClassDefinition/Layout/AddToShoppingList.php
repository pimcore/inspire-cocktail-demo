<?php

declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\ClassDefinition\Layout;

use Pimcore\Model\DataObject\ClassDefinition\Layout;

class AddToShoppingList extends Layout
{
    /** @internal */
    public string $fieldtype = 'addToShoppingList';

    /** @internal */
    public int $defaultAmount = 1;

    public function getDefaultAmount(): int
    {
        return $this->defaultAmount;
    }

    public function setDefaultAmount(int $defaultAmount): static
    {
        $this->defaultAmount = $defaultAmount;

        return $this;
    }
}
