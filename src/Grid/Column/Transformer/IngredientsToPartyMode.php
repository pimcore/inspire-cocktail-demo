<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Grid\Column\Transformer;

use Pimcore\Bundle\StudioBackendBundle\Element\Schema\RelatedElementData;
use Pimcore\Bundle\StudioBackendBundle\Grid\Column\TransformerInterface;
use Pimcore\Bundle\StudioBackendBundle\Grid\Util\AdvancedValue;
use Pimcore\Model\DataObject\Ingredient;

/**
 * @internal
 */
final class IngredientsToPartyMode implements TransformerInterface
{
    private const array UNIT_TO_ML = [
        'ml'   => 1.0,
        'cl'   => 10.0,
        'oz'   => 29.57,
        'dash' => 0.9,
        'tsp'  => 5.0,
        'tbsp' => 15.0,
    ];

    public function transform(array $value, array $config): array
    {
        $lines = $this->buildIngredientLines($value);

        if ($lines === []) {
            return [new AdvancedValue('wysiwyg', 'No ingredients — sad glass', 'ingredientsToPartyMode')];
        }

        return [new AdvancedValue('wysiwyg', implode('<br>', $lines), 'ingredientsToPartyMode')];
    }

    public function getName(): string
    {
        return 'Ingredients to PartyMode';
    }

    public function getKey(): string
    {
        return 'ingredientsToPartyMode';
    }

    public function getDescription(): string
    {
        return 'Normalizes ingredient amounts to ml and categorizes them with fun party labels.';
    }

    public function getConfigOptions(): array
    {
        return [];
    }

    /**
     * @param AdvancedValue[] $value
     *
     * @return string[]
     */
    private function buildIngredientLines(array $value): array
    {
        $ingredients = $value[0]->getValue();
        $lines = [];

        foreach ($ingredients as $ingredient) {
            $line = $this->processIngredient($ingredient);

            if ($line !== null) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    private function processIngredient(array $ingredient): ?string
    {
        if (!$ingredient['element'] instanceof RelatedElementData) {
            return null;
        }

        $ingredientObject = Ingredient::getById($ingredient['element']->getId());

        if ($ingredientObject === null) {
            return null;
        }

        $name = $ingredientObject->getName() ?? 'Unknown';
        $unit = $ingredientObject->getUnit() ?? 'ml';
        $amount = $this->extractAmount($ingredient);
        $category = $this->categorize($amount, $unit);

        return '<b>' . $name . '</b>: ' . $category;
    }

    private function extractAmount(array $ingredient): ?float
    {
        $amount = $ingredient['data']['amount'] ?? null;

        if ($amount === null) {
            return null;
        }

        return (float) $amount;
    }

    private function categorize(?float $amount, string $unit): string
    {
        if ($amount === null) {
            return 'mystery pour';
        }

        if ($unit === 'piece') {
            return $this->categorizePieces($amount);
        }

        $ml = $this->normalizeToMl($amount, $unit);

        return $this->categorizeLiquid($ml);
    }

    private function normalizeToMl(float $amount, string $unit): float
    {
        $factor = self::UNIT_TO_ML[$unit] ?? 1.0;

        return $amount * $factor;
    }

    private function categorizeLiquid(float $ml): string
    {
        return match (true) {
            $ml < 10  => 'just a whisper',
            $ml <= 20 => 'a gentle splash',
            $ml <= 30 => 'the sweet spot',
            $ml <= 50 => 'feeling generous',
            default   => 'someone\'s thirsty!',
        };
    }

    private function categorizePieces(float $amount): string
    {
        return match (true) {
            $amount <= 1 => 'a lonely one',
            $amount <= 3 => 'a little crew',
            $amount <= 6 => 'the more the merrier',
            default      => 'it\'s a whole garden!',
        };
    }
}
