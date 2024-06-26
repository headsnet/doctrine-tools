<?php

namespace Headsnet\DoctrineToolsBundle;

use Headsnet\DoctrineToolsBundle\Mapping\CarbonTypeMappingsCompilerPass;
use Headsnet\DoctrineToolsBundle\Mapping\DoctrineTypeMappingsCompilerPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class HeadsnetDoctrineToolsBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('custom_mappings')
                    ->children()
                        ->arrayNode('scan_dirs')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end() // End custom_mappings
                ->arrayNode('carbon_mappings')
                    ->canBeDisabled()
                    ->children()
                        ->booleanNode('replace')->defaultTrue()->end()
                    ->end()
                ->end() // End carbon_mappings
        ;
    }

    /**
     * @param array{
     *     custom_mappings: array{scan_dirs: array<string>},
     *     carbon_mappings: array{enabled: boolean, replace: boolean}
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->parameters()
            ->set('headsnet_doctrine_tools.custom_mappings.scan_dirs', $config['custom_mappings']['scan_dirs'])
            ->set('headsnet_doctrine_tools.carbon_mappings.enabled', $config['carbon_mappings']['enabled'])
            ->set('headsnet_doctrine_tools.carbon_mappings.replace', $config['carbon_mappings']['replace'])
        ;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new DoctrineTypeMappingsCompilerPass()
        );

        $container->addCompilerPass(
            new CarbonTypeMappingsCompilerPass()
        );
    }
}
