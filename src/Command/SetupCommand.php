<?php

declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Command;

use Pimcore\Bundle\GenericDataIndexBundle\Service\SearchIndex\IndexQueue\SynchronousProcessingServiceInterface;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\ClassDefinition\Service;
use Pimcore\Model\DataObject\Data\ObjectMetadata;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\SelectOptions\Config;
use Pimcore\Model\DataObject\SelectOptions\Data\SelectOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'cocktail-demo:setup',
    description: 'Creates Cocktail demo class definitions, Select Options, and sample data objects',
)]
class SetupCommand extends AbstractCommand
{
    public function __construct(
        private readonly SynchronousProcessingServiceInterface $synchronousProcessing,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        DataObject\AbstractObject::setHideUnpublished(false);
        $this->synchronousProcessing->enable();

        $this->createSelectOptions($output);
        $this->createClassDefinitions($output);
        $this->rebuildGenericDataIndex($output);
        $ingredientObjects = $this->createIngredients($output);
        $this->createCocktails($output, $ingredientObjects);

        $output->writeln('');
        $output->writeln('<comment>Please run bin/console cache:clear to activate the new class definitions.</comment>');

        return self::SUCCESS;
    }

    private function rebuildGenericDataIndex(OutputInterface $output): void
    {
        $output->writeln('<info>Rebuilding generic data index...</info>');

        $command = $this->getApplication()?->find('generic-data-index:update:index');
        if ($command === null) {
            $output->writeln('  <comment>[skip] generic-data-index:update:index command not available</comment>');

            return;
        }

        $returnCode = $command->run(new ArrayInput([]), $output);

        if ($returnCode === self::SUCCESS) {
            $output->writeln('  [ok] Generic data index updated');
        } else {
            $output->writeln('  <comment>[warn] Generic data index update returned code ' . $returnCode . '</comment>');
        }
    }

    private function createSelectOptions(OutputInterface $output): void
    {
        $output->writeln('<info>Setting up Select Options...</info>');

        $configs = [
            'Unit' => [
                'group'   => 'CocktailDemo',
                'options' => [
                    new SelectOption('ml', 'ml', 'Ml'),
                    new SelectOption('cl', 'cl', 'Cl'),
                    new SelectOption('oz', 'oz', 'Oz'),
                    new SelectOption('dash', 'Dash', 'Dash'),
                    new SelectOption('tsp', 'tsp', 'Tsp'),
                    new SelectOption('tbsp', 'tbsp', 'Tbsp'),
                    new SelectOption('piece', 'Piece', 'Piece'),
                ],
            ],
            'GlassType' => [
                'group'   => 'CocktailDemo',
                'options' => [
                    new SelectOption('rocks', 'Rocks', 'Rocks'),
                    new SelectOption('highball', 'Highball', 'Highball'),
                    new SelectOption('martini_coupe', 'Martini / Coupe', 'MartiniCoupe'),
                    new SelectOption('wine', 'Wine', 'Wine'),
                    new SelectOption('champagne_flute', 'Champagne Flute', 'ChampagneFlute'),
                    new SelectOption('shot', 'Shot', 'Shot'),
                    new SelectOption('mug', 'Mug', 'Mug'),
                ],
            ],
            'PreparationMethod' => [
                'group'   => 'CocktailDemo',
                'options' => [
                    new SelectOption('shaken', 'Shaken', 'Shaken'),
                    new SelectOption('stirred', 'Stirred', 'Stirred'),
                    new SelectOption('built', 'Built', 'Built'),
                    new SelectOption('blended', 'Blended', 'Blended'),
                    new SelectOption('layered', 'Layered', 'Layered'),
                ],
            ],
            'Strength' => [
                'group'   => 'CocktailDemo',
                'options' => [
                    new SelectOption('non_alcoholic', 'Non-alcoholic', 'NonAlcoholic'),
                    new SelectOption('low', 'Low', 'Low'),
                    new SelectOption('medium', 'Medium', 'Medium'),
                    new SelectOption('strong', 'Strong', 'Strong'),
                ],
            ],
            'FlavourProfile' => [
                'group'   => 'CocktailDemo',
                'options' => [
                    new SelectOption('bitter', 'Bitter', 'Bitter'),
                    new SelectOption('sweet', 'Sweet', 'Sweet'),
                    new SelectOption('sour', 'Sour', 'Sour'),
                    new SelectOption('fruity', 'Fruity', 'Fruity'),
                    new SelectOption('herbal', 'Herbal', 'Herbal'),
                    new SelectOption('spicy', 'Spicy', 'Spicy'),
                    new SelectOption('smoky', 'Smoky', 'Smoky'),
                    new SelectOption('creamy', 'Creamy', 'Creamy'),
                ],
            ],
            'Occasion' => [
                'group'   => 'CocktailDemo',
                'options' => [
                    new SelectOption('aperitivo', 'Aperitivo', 'Aperitivo'),
                    new SelectOption('after_dinner', 'After Dinner', 'AfterDinner'),
                    new SelectOption('party', 'Party', 'Party'),
                    new SelectOption('brunch', 'Brunch', 'Brunch'),
                    new SelectOption('summer', 'Summer', 'Summer'),
                    new SelectOption('winter', 'Winter', 'Winter'),
                ],
            ],
        ];

        foreach ($configs as $id => $definition) {
            if (Config::getById($id) !== null) {
                $output->writeln("  [skip] $id Select Options already exists");
                continue;
            }

            $config = new Config();
            $config->setId($id);
            $config->setGroup($definition['group']);
            $config->setSelectOptions(...$definition['options']);
            $config->save();

            $output->writeln("  [ok] $id Select Options created");
        }
    }

    private function createClassDefinitions(OutputInterface $output): void
    {
        $output->writeln('<info>Setting up class definitions...</info>');

        // Ingredient
        if (ClassDefinition::getByName('Ingredient') !== null) {
            $output->writeln('  [skip] Ingredient class already exists');
        } else {
            $class = new ClassDefinition();
            $class->setName('Ingredient');
            $class->setId('ingredient');
            $class->setUserOwner(0);
            $json = (string) file_get_contents(__DIR__ . '/../Resources/install/class_Ingredient_export.json');
            Service::importClassDefinitionFromJson($class, $json, false, true);
            $output->writeln('  [ok] Ingredient class created');
        }

        // Cocktail
        if (ClassDefinition::getByName('Cocktail') !== null) {
            $output->writeln('  [skip] Cocktail class already exists');
        } else {
            $class = new ClassDefinition();
            $class->setName('Cocktail');
            $class->setId('cocktail');
            $class->setUserOwner(0);
            $json = (string) file_get_contents(__DIR__ . '/../Resources/install/class_Cocktail_export.json');
            Service::importClassDefinitionFromJson($class, $json, false, true);
            $output->writeln('  [ok] Cocktail class created');
        }
    }

    /**
     * @return array<string, DataObject\Concrete>
     */
    private function createIngredients(OutputInterface $output): array
    {
        $output->writeln('<info>Setting up ingredient objects...</info>');

        $rootFolder = $this->ensureFolder('/Cocktail Demo', $output);
        $ingredientsFolder = $this->ensureFolder('/Cocktail Demo/Ingredients', $output, $rootFolder);

        $ingredientData = [
            // name => [unit, description]
            'Gin'             => ['ml', 'A classic London Dry gin'],
            'Sweet Vermouth'  => ['ml', 'Italian sweet red vermouth'],
            'Campari'         => ['ml', 'Italian bitter aperitivo'],
            'White Rum'       => ['ml', 'Light white rum'],
            'Lime Juice'      => ['ml', 'Freshly squeezed lime juice'],
            'Sugar Syrup'     => ['ml', 'Simple syrup (1:1 sugar to water)'],
            'Fresh Mint'      => ['piece', 'Fresh mint leaves'],
            'Soda Water'      => ['ml', 'Sparkling soda water'],
            'Vodka'           => ['ml', 'Premium vodka'],
            'Espresso'        => ['ml', 'Freshly brewed espresso, cooled'],
            'Coffee Liqueur'  => ['ml', 'Coffee-flavoured liqueur (e.g. Kahlúa)'],
            'Aperol'          => ['ml', 'Italian bitter aperitivo'],
            'Prosecco'        => ['ml', 'Italian sparkling white wine'],
        ];

        $objects = [];

        foreach ($ingredientData as $name => [$unit, $description]) {
            $key = $this->toObjectKey($name);
            $path = '/Cocktail Demo/Ingredients/' . $key;
            $existing = DataObject\Concrete::getByPath($path);

            if ($existing instanceof DataObject\Concrete) {
                $output->writeln("  [skip] Ingredient '$name' already exists");
                $objects[$name] = $existing;
                continue;
            }

            /** @var DataObject\Ingredient $ingredient */
            $ingredient = new DataObject\Ingredient();
            $ingredient->setParent($ingredientsFolder);
            $ingredient->setKey($key);
            $ingredient->setPublished(true);
            $ingredient->setName($name, 'en');
            $ingredient->setDescription($description, 'en');
            $ingredient->setUnit($unit);
            $ingredient->save();

            $output->writeln("  [ok] Created ingredient '$name'");
            $objects[$name] = $ingredient;
        }

        return $objects;
    }

    /**
     * @param array<string, DataObject\Concrete> $ingredientObjects
     */
    private function createCocktails(OutputInterface $output, array $ingredientObjects): void
    {
        $output->writeln('<info>Setting up cocktail objects...</info>');

        $cocktailsFolder = $this->ensureFolder('/Cocktail Demo/Cocktails', $output,
            DataObject\Folder::getByPath('/Cocktail Demo')
        );

        $cocktails = [
            'Negroni' => [
                'description'       => 'A classic Italian aperitivo cocktail with equal parts gin, sweet vermouth, and Campari.',
                'instructions'      => '<p>Combine all ingredients in a mixing glass filled with ice. Stir until well chilled. Strain into a rocks glass over a large ice cube. Garnish with an orange peel.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'stirred',
                'strength'          => 'strong',
                'flavourProfile'    => ['bitter', 'herbal', 'sweet'],
                'occasion'          => ['aperitivo', 'after_dinner'],
                'ingredients'       => [
                    ['Gin',            30, 'London Dry preferred'],
                    ['Sweet Vermouth', 30, ''],
                    ['Campari',        30, ''],
                ],
            ],
            'Mojito' => [
                'description'       => 'A refreshing Cuban highball with white rum, lime, sugar, mint, and soda.',
                'instructions'      => '<p>Muddle mint leaves with lime juice and sugar syrup in a highball glass. Add rum and fill with ice. Top with soda water and stir gently. Garnish with a mint sprig.</p>',
                'glassType'         => 'highball',
                'preparationMethod' => 'built',
                'strength'          => 'medium',
                'flavourProfile'    => ['sour', 'sweet', 'herbal'],
                'occasion'          => ['summer', 'party'],
                'ingredients'       => [
                    ['White Rum',   50, ''],
                    ['Lime Juice',  25, 'Freshly squeezed'],
                    ['Sugar Syrup', 15, ''],
                    ['Fresh Mint',   8, 'Leaves only'],
                    ['Soda Water',  60, 'To top'],
                ],
            ],
            'Espresso Martini' => [
                'description'       => 'A chilled espresso cocktail with vodka and coffee liqueur, topped with a rich crema.',
                'instructions'      => '<p>Add all ingredients to a cocktail shaker filled with ice. Shake vigorously until well chilled and frothy. Double strain into a chilled martini glass. Garnish with three coffee beans.</p>',
                'glassType'         => 'martini_coupe',
                'preparationMethod' => 'shaken',
                'strength'          => 'strong',
                'flavourProfile'    => ['bitter', 'sweet', 'creamy'],
                'occasion'          => ['after_dinner', 'party'],
                'ingredients'       => [
                    ['Vodka',          50, 'Premium vodka'],
                    ['Espresso',       30, 'Freshly brewed and cooled'],
                    ['Coffee Liqueur', 20, 'Kahlúa or similar'],
                    ['Sugar Syrup',    10, 'Optional, to taste'],
                ],
            ],
            'Aperol Spritz' => [
                'description'       => 'A light and bubbly Italian aperitivo cocktail with Aperol, Prosecco, and soda.',
                'instructions'      => '<p>Build in a large wine glass filled with ice. Pour Prosecco first, then Aperol, then a splash of soda water. Stir gently. Garnish with a slice of orange.</p>',
                'glassType'         => 'wine',
                'preparationMethod' => 'built',
                'strength'          => 'low',
                'flavourProfile'    => ['bitter', 'fruity', 'sweet'],
                'occasion'          => ['aperitivo', 'summer', 'brunch'],
                'ingredients'       => [
                    ['Aperol',     60, ''],
                    ['Prosecco',   90, ''],
                    ['Soda Water', 30, 'A splash'],
                ],
            ],
        ];

        foreach ($cocktails as $name => $data) {
            $key = $this->toObjectKey($name);
            $path = '/Cocktail Demo/Cocktails/' . $key;
            $existing = DataObject\Concrete::getByPath($path);

            if ($existing instanceof DataObject\Concrete) {
                $output->writeln("  [skip] Cocktail '$name' already exists");
                continue;
            }

            $metadataItems = [];
            foreach ($data['ingredients'] as [$ingredientName, $amount, $notes]) {
                if (!isset($ingredientObjects[$ingredientName])) {
                    $output->writeln("  <comment>[warn] Ingredient '$ingredientName' not found, skipping for '$name'</comment>");
                    continue;
                }
                $meta = new ObjectMetadata('ingredients', ['amount', 'notes'], $ingredientObjects[$ingredientName]);
                $meta->setAmount($amount);
                $meta->setNotes($notes);
                $metadataItems[] = $meta;
            }

            /** @var DataObject\Cocktail $cocktail */
            $cocktail = new DataObject\Cocktail();
            $cocktail->setParent($cocktailsFolder);
            $cocktail->setKey($key);
            $cocktail->setPublished(true);
            $cocktail->setName($name, 'en');
            $cocktail->setDescription($data['description'], 'en');
            $cocktail->setInstructions($data['instructions'], 'en');
            $cocktail->setGlassType($data['glassType']);
            $cocktail->setPreparationMethod($data['preparationMethod']);
            $cocktail->setStrength($data['strength']);
            $cocktail->setFlavourProfile($data['flavourProfile']);
            $cocktail->setOccasion($data['occasion']);
            $cocktail->setIngredients($metadataItems);
            $cocktail->save();

            $output->writeln("  [ok] Created cocktail '$name'");
        }
    }

    private function ensureFolder(string $path, OutputInterface $output, ?Folder $parent = null): Folder
    {
        $existing = Folder::getByPath($path);
        if ($existing instanceof Folder) {
            return $existing;
        }

        $folder = new Folder();
        $folder->setKey(basename($path));
        $folder->setParent($parent ?? DataObject\Folder::getByPath('/'));
        $folder->save();

        $output->writeln("  [ok] Created folder '$path'");

        return $folder;
    }

    private function toObjectKey(string $name): string
    {
        return strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    }
}
