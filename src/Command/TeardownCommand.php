<?php

declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\Command;

use Pimcore\Bundle\GenericDataIndexBundle\Service\SearchIndex\IndexQueue\SynchronousProcessingServiceInterface;
use Pimcore\Console\AbstractCommand;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\SelectOptions\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'cocktail-demo:teardown',
    description: 'Removes Cocktail demo class definitions, Select Options, and the /Cocktail Demo data object folder',
)]
class TeardownCommand extends AbstractCommand
{
    public function __construct(
        private readonly SynchronousProcessingServiceInterface $synchronousProcessing,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Skip confirmation prompt',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input->getOption('force')) {
            /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
            $helper = $this->getHelper('question');
            $question = new \Symfony\Component\Console\Question\ConfirmationQuestion(
                '<question>This will permanently delete the Cocktail/Ingredient classes, all Select Options, and all objects under /Cocktail Demo. Continue? [y/N] </question>',
                false,
            );

            if (!$helper->ask($input, $output, $question)) {
                $output->writeln('Aborted.');

                return self::SUCCESS;
            }
        }

        $this->synchronousProcessing->enable();

        $this->deleteFolder($output);
        $this->deleteClassDefinitions($output);
        $this->deleteSelectOptions($output);

        $output->writeln('');
        $output->writeln('<info>Teardown complete. Run bin/console cache:clear to finish.</info>');

        return self::SUCCESS;
    }

    private function deleteFolder(OutputInterface $output): void
    {
        $output->writeln('<info>Deleting /Cocktail Demo folder...</info>');

        $folder = Folder::getByPath('/Cocktail Demo');
        if (!$folder instanceof Folder) {
            $output->writeln('  [skip] /Cocktail Demo folder not found');

            return;
        }

        $folder->delete();
        $output->writeln('  [ok] Deleted /Cocktail Demo (including all child objects)');
    }

    private function deleteClassDefinitions(OutputInterface $output): void
    {
        $output->writeln('<info>Deleting class definitions...</info>');

        foreach (['Cocktail', 'Ingredient'] as $name) {
            $class = ClassDefinition::getByName($name);
            if ($class === null) {
                $output->writeln("  [skip] $name class not found");
                continue;
            }

            $class->delete();
            $output->writeln("  [ok] Deleted $name class");
        }
    }

    private function deleteSelectOptions(OutputInterface $output): void
    {
        $output->writeln('<info>Deleting Select Options...</info>');

        foreach (['Unit', 'GlassType', 'PreparationMethod', 'Strength', 'FlavourProfile', 'Occasion'] as $id) {
            $config = Config::getById($id);
            if ($config === null) {
                $output->writeln("  [skip] $id Select Options not found");
                continue;
            }

            $config->delete();
            $output->writeln("  [ok] Deleted $id Select Options");
        }
    }
}
