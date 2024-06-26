<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Mapping;

use Carbon\Doctrine\DateTimeImmutableType;
use Carbon\Doctrine\DateTimeType;
use Headsnet\DoctrineToolsBundle\Mapping\CarbonTypeMappingsCompilerPass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(CarbonTypeMappingsCompilerPass::class)]
class CarbonTypeMappingsCompilerPassTest extends TestCase
{
    #[Test]
    public function carbon_mappings_are_registered(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.dbal.connection_factory.types', []);
        $container->setParameter('headsnet_doctrine_tools.carbon_mappings.enabled', true);
        $container->setParameter('headsnet_doctrine_tools.carbon_mappings.replace', true);
        $sut = new CarbonTypeMappingsCompilerPass();

        $sut->process($container);

        $result = $container->getParameter('doctrine.dbal.connection_factory.types');
        $expected = [
            'datetime_immutable' => [
                'class' => DateTimeImmutableType::class,
            ],
            'datetime' => [
                'class' => DateTimeType::class,
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function carbon_mappings_are_registered_separately(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.dbal.connection_factory.types', []);
        $container->setParameter('headsnet_doctrine_tools.carbon_mappings.enabled', true);
        $container->setParameter('headsnet_doctrine_tools.carbon_mappings.replace', false);
        $sut = new CarbonTypeMappingsCompilerPass();

        $sut->process($container);

        $result = $container->getParameter('doctrine.dbal.connection_factory.types');
        $expected = [
            'carbon_immutable' => [
                'class' => DateTimeImmutableType::class,
            ],
            'carbon' => [
                'class' => DateTimeType::class,
            ],
        ];
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function if_disabled_then_register_nothing(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('doctrine.dbal.connection_factory.types', []);
        $container->setParameter('headsnet_doctrine_tools.carbon_mappings.enabled', false);
        $sut = new CarbonTypeMappingsCompilerPass();

        $sut->process($container);

        $result = $container->getParameter('doctrine.dbal.connection_factory.types');
        $expected = [];
        $this->assertEquals($expected, $result);
    }
}
