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
            'Gin'                    => ['ml', 'A classic London Dry gin'],
            'Sweet Vermouth'         => ['ml', 'Italian sweet red vermouth'],
            'Campari'                => ['ml', 'Italian bitter aperitivo'],
            'White Rum'              => ['ml', 'Light white rum'],
            'Dark Rum'               => ['ml', 'Aged dark rum'],
            'Lime Juice'             => ['ml', 'Freshly squeezed lime juice'],
            'Lemon Juice'            => ['ml', 'Freshly squeezed lemon juice'],
            'Orange Juice'           => ['ml', 'Freshly squeezed orange juice'],
            'Pineapple Juice'        => ['ml', 'Fresh pineapple juice'],
            'Cranberry Juice'        => ['ml', 'Unsweetened cranberry juice'],
            'Grapefruit Juice'       => ['ml', 'Freshly squeezed grapefruit juice'],
            'Coconut Cream'          => ['ml', 'Full-fat coconut cream'],
            'Sugar Syrup'            => ['ml', 'Simple syrup (1:1 sugar to water)'],
            'Honey Syrup'            => ['ml', 'Honey diluted with warm water (2:1)'],
            'Grenadine'              => ['ml', 'Pomegranate-based syrup'],
            'Fresh Mint'             => ['piece', 'Fresh mint leaves'],
            'Soda Water'             => ['ml', 'Sparkling soda water'],
            'Ginger Beer'            => ['ml', 'Spicy ginger beer'],
            'Tonic Water'            => ['ml', 'Tonic water'],
            'Vodka'                  => ['ml', 'Premium vodka'],
            'Tequila'                => ['ml', 'Blanco tequila'],
            'Mezcal'                 => ['ml', 'Smoky artisanal mezcal'],
            'Bourbon'                => ['ml', 'American bourbon whiskey'],
            'Scotch Whisky'          => ['ml', 'Blended Scotch whisky'],
            'Rye Whiskey'            => ['ml', 'Spicy rye whiskey'],
            'Espresso'               => ['ml', 'Freshly brewed espresso, cooled'],
            'Coffee Liqueur'         => ['ml', 'Coffee-flavoured liqueur (e.g. Kahlúa)'],
            'Aperol'                 => ['ml', 'Italian bitter aperitivo'],
            'Prosecco'               => ['ml', 'Italian sparkling white wine'],
            'Champagne'              => ['ml', 'Dry Champagne or Crémant'],
            'Dry Vermouth'           => ['ml', 'French dry white vermouth'],
            'Blue Curaçao'           => ['ml', 'Orange-flavoured blue liqueur'],
            'Triple Sec'             => ['ml', 'Orange liqueur (e.g. Cointreau)'],
            'Elderflower Liqueur'    => ['ml', 'Elderflower-infused liqueur'],
            'Peach Schnapps'         => ['ml', 'Peach-flavoured schnapps'],
            'Amaretto'               => ['ml', 'Almond-flavoured Italian liqueur'],
            'Baileys'                => ['ml', 'Irish cream liqueur'],
            'Angostura Bitters'      => ['dash', 'Aromatic bitters'],
            'Orange Bitters'         => ['dash', 'Orange aromatic bitters'],
            'Egg White'              => ['piece', 'One fresh egg white for foam'],
            'Heavy Cream'            => ['ml', 'Full-fat pouring cream'],
            'Cucumber'               => ['piece', 'Fresh cucumber slices'],
            'Jalapeño'               => ['piece', 'Fresh jalapeño slices'],
            'Ginger'                 => ['piece', 'Fresh ginger slices'],
            'Salt'                   => ['piece', 'For rim salting'],
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
            // ── EXISTING 4 ───────────────────────────────────────────────────
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

            // ── NEW: STRONG ──────────────────────────────────────────────────
            'Old Fashioned' => [
                'description'       => 'The original cocktail — bourbon, sugar, and bitters stirred down with an orange twist.',
                'instructions'      => '<p>Place sugar cube in a rocks glass and saturate with bitters. Add a splash of soda water and muddle. Add bourbon and a large ice cube. Stir and garnish with an orange twist.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'stirred',
                'strength'          => 'strong',
                'flavourProfile'    => ['sweet', 'bitter', 'smoky'],
                'occasion'          => ['after_dinner'],
                'ingredients'       => [
                    ['Bourbon',           60, 'Or rye whiskey'],
                    ['Sugar Syrup',        5, ''],
                    ['Angostura Bitters',  2, '2 dashes'],
                    ['Orange Bitters',     1, '1 dash'],
                ],
            ],
            'Dry Martini' => [
                'description'       => 'The quintessential classic — gin and dry vermouth stirred ice-cold.',
                'instructions'      => '<p>Stir gin and vermouth in a mixing glass with ice until very cold. Strain into a chilled martini glass. Garnish with a lemon twist or olive.</p>',
                'glassType'         => 'martini_coupe',
                'preparationMethod' => 'stirred',
                'strength'          => 'strong',
                'flavourProfile'    => ['herbal', 'bitter'],
                'occasion'          => ['aperitivo', 'after_dinner'],
                'ingredients'       => [
                    ['Gin',          75, 'London Dry'],
                    ['Dry Vermouth', 15, ''],
                ],
            ],
            'Penicillin' => [
                'description'       => 'A modern classic — blended Scotch, honey-lemon, and a smoky mezcal float.',
                'instructions'      => '<p>Shake Scotch, lemon juice, and honey syrup with ice. Strain into a rocks glass over ice. Float mezcal on top. Garnish with candied ginger.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'shaken',
                'strength'          => 'strong',
                'flavourProfile'    => ['smoky', 'sour', 'sweet'],
                'occasion'          => ['after_dinner'],
                'ingredients'       => [
                    ['Scotch Whisky', 50, 'Blended'],
                    ['Lemon Juice',   25, 'Freshly squeezed'],
                    ['Honey Syrup',   20, '2:1 honey to water'],
                    ['Mezcal',        10, 'Float on top'],
                    ['Ginger',         2, 'Slices, muddled'],
                ],
            ],
            'Manhattan' => [
                'description'       => 'A bold, boozy classic of rye whiskey and sweet vermouth with aromatic bitters.',
                'instructions'      => '<p>Stir all ingredients in a mixing glass with ice. Strain into a chilled coupe. Garnish with a maraschino cherry.</p>',
                'glassType'         => 'martini_coupe',
                'preparationMethod' => 'stirred',
                'strength'          => 'strong',
                'flavourProfile'    => ['sweet', 'bitter', 'herbal'],
                'occasion'          => ['after_dinner'],
                'ingredients'       => [
                    ['Rye Whiskey',      60, ''],
                    ['Sweet Vermouth',   30, ''],
                    ['Angostura Bitters', 2, '2 dashes'],
                ],
            ],
            'Margarita' => [
                'description'       => 'The world\'s most popular cocktail — tequila, lime, and orange liqueur with a salted rim.',
                'instructions'      => '<p>Salt the rim of a rocks glass. Shake tequila, lime juice, and triple sec with ice. Strain into the glass over ice. Garnish with a lime wedge.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'shaken',
                'strength'          => 'strong',
                'flavourProfile'    => ['sour', 'fruity'],
                'occasion'          => ['party', 'summer'],
                'ingredients'       => [
                    ['Tequila',    50, 'Blanco'],
                    ['Lime Juice', 25, 'Freshly squeezed'],
                    ['Triple Sec', 25, 'Cointreau preferred'],
                    ['Salt',        1, 'For rim'],
                ],
            ],
            'Mezcal Negroni' => [
                'description'       => 'A smoky spin on the Negroni with mezcal replacing gin.',
                'instructions'      => '<p>Stir all ingredients in a mixing glass with ice. Strain into a rocks glass over a large ice cube. Garnish with a flamed orange peel.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'stirred',
                'strength'          => 'strong',
                'flavourProfile'    => ['smoky', 'bitter', 'sweet'],
                'occasion'          => ['aperitivo'],
                'ingredients'       => [
                    ['Mezcal',        30, ''],
                    ['Sweet Vermouth', 30, ''],
                    ['Campari',        30, ''],
                ],
            ],

            // ── NEW: MEDIUM ──────────────────────────────────────────────────
            'Whisky Sour' => [
                'description'       => 'A silky bourbon sour with a cloud of egg-white foam.',
                'instructions'      => '<p>Dry shake all ingredients first. Add ice and shake again. Strain into a rocks glass over ice or a coupe. Garnish with a cherry and orange slice.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'shaken',
                'strength'          => 'medium',
                'flavourProfile'    => ['sour', 'sweet', 'creamy'],
                'occasion'          => ['party', 'brunch'],
                'ingredients'       => [
                    ['Bourbon',     50, ''],
                    ['Lemon Juice', 30, 'Freshly squeezed'],
                    ['Sugar Syrup', 20, ''],
                    ['Egg White',    1, '1 egg white'],
                ],
            ],
            'Cosmopolitan' => [
                'description'       => 'A glamorous pink cocktail with vodka, cranberry, lime, and orange.',
                'instructions'      => '<p>Shake all ingredients vigorously with ice. Double strain into a chilled martini glass. Garnish with a flamed orange twist.</p>',
                'glassType'         => 'martini_coupe',
                'preparationMethod' => 'shaken',
                'strength'          => 'medium',
                'flavourProfile'    => ['fruity', 'sour'],
                'occasion'          => ['party', 'brunch'],
                'ingredients'       => [
                    ['Vodka',           45, ''],
                    ['Triple Sec',      15, ''],
                    ['Cranberry Juice', 30, 'Unsweetened'],
                    ['Lime Juice',      10, 'Freshly squeezed'],
                ],
            ],
            'Dark and Stormy' => [
                'description'       => 'A bold Bermudian highball of dark rum and fiery ginger beer.',
                'instructions'      => '<p>Fill a highball glass with ice. Pour ginger beer then float dark rum on top. Squeeze in a lime wedge and drop it in.</p>',
                'glassType'         => 'highball',
                'preparationMethod' => 'built',
                'strength'          => 'medium',
                'flavourProfile'    => ['spicy', 'sweet'],
                'occasion'          => ['party', 'summer'],
                'ingredients'       => [
                    ['Dark Rum',    60, 'Goslings Black Seal preferred'],
                    ['Ginger Beer', 120, 'Spicy variety'],
                    ['Lime Juice',  10, 'A squeeze'],
                ],
            ],
            'Paloma' => [
                'description'       => 'Mexico\'s favourite cocktail — tequila and fresh grapefruit with a salty rim.',
                'instructions'      => '<p>Salt the rim of a highball glass. Add ice, tequila, lime juice, and top with grapefruit juice. Stir gently and garnish with a grapefruit wedge.</p>',
                'glassType'         => 'highball',
                'preparationMethod' => 'built',
                'strength'          => 'medium',
                'flavourProfile'    => ['sour', 'fruity'],
                'occasion'          => ['summer', 'brunch'],
                'ingredients'       => [
                    ['Tequila',          50, 'Blanco'],
                    ['Grapefruit Juice', 90, 'Freshly squeezed'],
                    ['Lime Juice',       15, ''],
                    ['Sugar Syrup',      10, ''],
                    ['Salt',              1, 'For rim'],
                ],
            ],
            'Tom Collins' => [
                'description'       => 'A tall, refreshing gin sour lengthened with soda — the original summer drink.',
                'instructions'      => '<p>Shake gin, lemon juice, and sugar syrup with ice. Strain into a highball glass filled with ice. Top with soda water. Garnish with a lemon slice and cherry.</p>',
                'glassType'         => 'highball',
                'preparationMethod' => 'shaken',
                'strength'          => 'medium',
                'flavourProfile'    => ['sour', 'herbal'],
                'occasion'          => ['summer', 'brunch'],
                'ingredients'       => [
                    ['Gin',         50, ''],
                    ['Lemon Juice', 30, 'Freshly squeezed'],
                    ['Sugar Syrup', 15, ''],
                    ['Soda Water',  60, 'To top'],
                ],
            ],
            'Moscow Mule' => [
                'description'       => 'Vodka and spicy ginger beer over ice — simple, punchy, and served in a copper mug.',
                'instructions'      => '<p>Fill a copper mug or highball glass with ice. Add vodka and lime juice. Top with ginger beer and stir gently. Garnish with a lime wheel and mint sprig.</p>',
                'glassType'         => 'mug',
                'preparationMethod' => 'built',
                'strength'          => 'medium',
                'flavourProfile'    => ['spicy', 'sour'],
                'occasion'          => ['party'],
                'ingredients'       => [
                    ['Vodka',       50, ''],
                    ['Lime Juice',  15, 'Freshly squeezed'],
                    ['Ginger Beer', 120, 'Fiery variety'],
                ],
            ],
            'Jungle Bird' => [
                'description'       => 'A surprisingly balanced tiki cocktail with rum, Campari, pineapple, and lime.',
                'instructions'      => '<p>Shake all ingredients with ice. Strain into a rocks glass or tiki mug over crushed ice. Garnish with a pineapple wedge.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'shaken',
                'strength'          => 'medium',
                'flavourProfile'    => ['bitter', 'fruity', 'sweet'],
                'occasion'          => ['party', 'summer'],
                'ingredients'       => [
                    ['Dark Rum',        45, ''],
                    ['Campari',         20, ''],
                    ['Pineapple Juice', 45, ''],
                    ['Lime Juice',      15, ''],
                    ['Sugar Syrup',     10, ''],
                ],
            ],
            'Clover Club' => [
                'description'       => 'A pre-Prohibition gin sour with raspberry and a silky egg-white foam.',
                'instructions'      => '<p>Dry shake all ingredients first. Add ice and shake again vigorously. Fine strain into a chilled coupe. No garnish needed.</p>',
                'glassType'         => 'martini_coupe',
                'preparationMethod' => 'shaken',
                'strength'          => 'medium',
                'flavourProfile'    => ['fruity', 'sour', 'creamy'],
                'occasion'          => ['brunch', 'aperitivo'],
                'ingredients'       => [
                    ['Gin',         50, ''],
                    ['Lemon Juice', 25, ''],
                    ['Grenadine',   15, 'Or raspberry syrup'],
                    ['Egg White',    1, '1 egg white'],
                ],
            ],
            'Amaretto Sour' => [
                'description'       => 'A luxuriously creamy sour with almond sweetness and a velvety egg-white top.',
                'instructions'      => '<p>Combine amaretto, lemon juice, and egg white in a shaker. Dry shake, then add ice and shake again. Strain over ice in a rocks glass. Garnish with cherry and orange.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'shaken',
                'strength'          => 'medium',
                'flavourProfile'    => ['sweet', 'sour', 'creamy'],
                'occasion'          => ['after_dinner', 'brunch'],
                'ingredients'       => [
                    ['Amaretto',    45, ''],
                    ['Bourbon',     15, 'A small measure for backbone'],
                    ['Lemon Juice', 30, 'Freshly squeezed'],
                    ['Egg White',    1, '1 egg white'],
                ],
            ],

            // ── NEW: LOW ─────────────────────────────────────────────────────
            'French 75' => [
                'description'       => 'A sparkling Champagne cocktail with gin and lemon — elegant and celebratory.',
                'instructions'      => '<p>Shake gin, lemon juice, and sugar syrup with ice. Strain into a Champagne flute. Top with Champagne. Garnish with a lemon twist.</p>',
                'glassType'         => 'champagne_flute',
                'preparationMethod' => 'shaken',
                'strength'          => 'low',
                'flavourProfile'    => ['sour', 'fruity'],
                'occasion'          => ['brunch', 'aperitivo'],
                'ingredients'       => [
                    ['Gin',        30, ''],
                    ['Lemon Juice', 15, 'Freshly squeezed'],
                    ['Sugar Syrup', 10, ''],
                    ['Champagne',  90, 'To top'],
                ],
            ],
            'Elderflower Spritz' => [
                'description'       => 'A floral, aromatic spritz with elderflower liqueur, cucumber, and Prosecco.',
                'instructions'      => '<p>Add elderflower liqueur and cucumber to a wine glass filled with ice. Top with Prosecco and a splash of soda. Stir gently. Garnish with cucumber and mint.</p>',
                'glassType'         => 'wine',
                'preparationMethod' => 'built',
                'strength'          => 'low',
                'flavourProfile'    => ['herbal', 'sweet', 'fruity'],
                'occasion'          => ['brunch', 'summer', 'aperitivo'],
                'ingredients'       => [
                    ['Elderflower Liqueur', 50, 'St-Germain or similar'],
                    ['Cucumber',             3, 'Slices'],
                    ['Prosecco',           100, ''],
                    ['Soda Water',          30, 'A splash'],
                ],
            ],
            'Peach Bellini' => [
                'description'       => 'Venice\'s beloved brunch cocktail — white peach purée and ice-cold Prosecco.',
                'instructions'      => '<p>Pour chilled peach schnapps or peach purée into a Champagne flute. Top with cold Prosecco and stir very gently. No garnish needed.</p>',
                'glassType'         => 'champagne_flute',
                'preparationMethod' => 'built',
                'strength'          => 'low',
                'flavourProfile'    => ['fruity', 'sweet'],
                'occasion'          => ['brunch', 'aperitivo'],
                'ingredients'       => [
                    ['Peach Schnapps', 50, 'Or fresh white peach purée'],
                    ['Prosecco',      100, 'Well chilled'],
                ],
            ],
            'Gin and Tonic' => [
                'description'       => 'The definitive serve — premium gin with aromatic tonic water.',
                'instructions'      => '<p>Fill a large wine glass or highball with ice. Pour gin over the ice. Add tonic water and stir briefly. Garnish with a lime wedge or cucumber slice.</p>',
                'glassType'         => 'wine',
                'preparationMethod' => 'built',
                'strength'          => 'low',
                'flavourProfile'    => ['bitter', 'herbal'],
                'occasion'          => ['aperitivo', 'summer'],
                'ingredients'       => [
                    ['Gin',         50, 'Botanical or London Dry'],
                    ['Tonic Water', 150, 'Premium tonic'],
                ],
            ],
            'Kir Royale' => [
                'description'       => 'A Burgundian classic — crème de cassis topped with Champagne.',
                'instructions'      => '<p>Pour crème de cassis into a Champagne flute. Top gently with cold Champagne. Do not stir. Garnish with a fresh blackberry.</p>',
                'glassType'         => 'champagne_flute',
                'preparationMethod' => 'built',
                'strength'          => 'low',
                'flavourProfile'    => ['fruity', 'sweet'],
                'occasion'          => ['aperitivo', 'brunch'],
                'ingredients'       => [
                    ['Grenadine',   15, 'Or crème de cassis'],
                    ['Champagne',  120, 'Well chilled'],
                ],
            ],

            // ── NEW: AFTER DINNER SPECIALS ───────────────────────────────────
            'White Russian' => [
                'description'       => 'Vodka, coffee liqueur, and heavy cream over ice — rich, indulgent, and iconic.',
                'instructions'      => '<p>Fill a rocks glass with ice. Add vodka and coffee liqueur. Float heavy cream on top by pouring it gently over the back of a spoon.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'built',
                'strength'          => 'medium',
                'flavourProfile'    => ['creamy', 'sweet', 'bitter'],
                'occasion'          => ['after_dinner'],
                'ingredients'       => [
                    ['Vodka',          50, ''],
                    ['Coffee Liqueur', 25, 'Kahlúa'],
                    ['Heavy Cream',    25, 'Float on top'],
                ],
            ],
            'Baileys on the Rocks' => [
                'description'       => 'Irish cream liqueur poured over ice — simple, creamy, and satisfying.',
                'instructions'      => '<p>Pour Baileys over a large ice cube in a rocks glass. Optionally garnish with freshly grated nutmeg.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'built',
                'strength'          => 'low',
                'flavourProfile'    => ['creamy', 'sweet'],
                'occasion'          => ['after_dinner'],
                'ingredients'       => [
                    ['Baileys', 75, 'Serve cold'],
                ],
            ],

            // ── NEW: SPICY / SMOKY ───────────────────────────────────────────
            'Spicy Margarita' => [
                'description'       => 'A classic Margarita with a fiery jalapeño kick — addictive and bold.',
                'instructions'      => '<p>Muddle jalapeño slices in a shaker. Add tequila, lime juice, triple sec, and ice. Shake hard. Fine strain into a salt-rimmed rocks glass over ice.</p>',
                'glassType'         => 'rocks',
                'preparationMethod' => 'shaken',
                'strength'          => 'strong',
                'flavourProfile'    => ['spicy', 'sour', 'fruity'],
                'occasion'          => ['party'],
                'ingredients'       => [
                    ['Tequila',    50, 'Blanco or reposado'],
                    ['Lime Juice', 25, 'Freshly squeezed'],
                    ['Triple Sec', 20, ''],
                    ['Jalapeño',    3, 'Slices, adjust to heat preference'],
                    ['Salt',        1, 'For rim'],
                ],
            ],
            'Tequila Sunrise' => [
                'description'       => 'A vibrant layered cocktail with tequila, orange juice, and a sunset of grenadine.',
                'instructions'      => '<p>Add tequila and orange juice to a highball glass filled with ice. Slowly pour grenadine down the side of the glass — it will sink and create the sunrise effect. Do not stir.</p>',
                'glassType'         => 'highball',
                'preparationMethod' => 'built',
                'strength'          => 'medium',
                'flavourProfile'    => ['fruity', 'sweet'],
                'occasion'          => ['brunch', 'party', 'summer'],
                'ingredients'       => [
                    ['Tequila',       50, ''],
                    ['Orange Juice', 120, 'Freshly squeezed'],
                    ['Grenadine',     15, 'Float at the bottom'],
                ],
            ],
            'Piña Colada' => [
                'description'       => 'The ultimate tropical escape — white rum, coconut cream, and pineapple blended smooth.',
                'instructions'      => '<p>Blend rum, coconut cream, and pineapple juice with a cup of crushed ice until smooth. Pour into a highball or hurricane glass. Garnish with a pineapple wedge and cherry.</p>',
                'glassType'         => 'highball',
                'preparationMethod' => 'blended',
                'strength'          => 'medium',
                'flavourProfile'    => ['creamy', 'fruity', 'sweet'],
                'occasion'          => ['summer', 'party'],
                'ingredients'       => [
                    ['White Rum',       50, ''],
                    ['Coconut Cream',   30, ''],
                    ['Pineapple Juice', 90, ''],
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
