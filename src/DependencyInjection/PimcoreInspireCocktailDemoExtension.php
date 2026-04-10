<?php
declare(strict_types=1);

namespace Pimcore\Bundle\InspireCocktailDemoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PimcoreInspireCocktailDemoExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');
        $loader->load('studio_backend.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('pimcore/studio_backend.yaml');

        // Register our custom layout namespace so Pimcore's layout loader can
        // resolve fieldtype 'addToShoppingList' → AddToShoppingList::class
        $container->prependExtensionConfig('pimcore', [
            'objects' => [
                'class_definitions' => [
                    'layout' => [
                        'prefixes' => [
                            'Pimcore\Bundle\InspireCocktailDemoBundle\ClassDefinition\Layout\\',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
