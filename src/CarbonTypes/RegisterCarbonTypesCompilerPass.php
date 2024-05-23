<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\CarbonTypes;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Automatically registers the custom Doctrine types provided by the Carbon library.
 */
final class RegisterCarbonTypesCompilerPass implements CompilerPassInterface
{
    private const TYPE_DEFINITION_PARAMETER = 'doctrine.dbal.connection_factory.types';

    public function process(ContainerBuilder $container): void
    {
        // Skip if carbon_types is disabled in the configuration
        if (!$container->getParameter('headsnet_doctrine_tools.carbon_types.enabled')) {
            return;
        }

        // Skip if Doctrine is not installed
        if (!$container->hasParameter(self::TYPE_DEFINITION_PARAMETER)) {
            return;
        }

        // Skip if Carbon is not installed
        if (!class_exists('Carbon\Carbon')) {
            return;
        }

        /** @var array<string, array{class: class-string}> $typeDefinitions */
        $typeDefinitions = $container->getParameter(self::TYPE_DEFINITION_PARAMETER);

        // Use Carbon to upgrade standard datetime and datetime_immutable column types
        if ($container->getParameter('headsnet_doctrine_tools.carbon_types.replace')) {
            $immutableName = 'datetime_immutable';
            $mutableName = 'datetime';
        }
        // Otherwise,  register additional types and leave the existing datetime and
        // datetime_immutable types in place
        else {
            $immutableName = 'carbon_immutable';
            $mutableName = 'carbon';
        }

        $typeDefinitions[$immutableName] = [
            'class' => 'Carbon\Doctrine\DateTimeImmutableType',
        ];

        $typeDefinitions[$mutableName] = [
            'class' => 'Carbon\Doctrine\DateTimeType',
        ];

        $container->setParameter(self::TYPE_DEFINITION_PARAMETER, $typeDefinitions);
    }
}
