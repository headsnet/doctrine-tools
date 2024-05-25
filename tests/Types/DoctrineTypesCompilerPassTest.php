<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types;

use Headsnet\DoctrineToolsBundle\HeadsnetDoctrineToolsBundle;
use Headsnet\DoctrineToolsBundle\Types\CandidateType;
use Headsnet\DoctrineToolsBundle\Types\DoctrineTypesCompilerPass;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\IntegerMappingPrototype;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\StringMappingPrototype;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\UuidMappingPrototype;
use Nyholm\BundleTest\TestKernel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

#[CoversClass(DoctrineTypesCompilerPass::class)]
#[CoversClass(StringMappingPrototype::class)]
#[CoversClass(IntegerMappingPrototype::class)]
#[CoversClass(UuidMappingPrototype::class)]
#[CoversClass(CandidateType::class)]
class DoctrineTypesCompilerPassTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * @param array{debug?: bool, environment?: string} $options
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        /** @var TestKernel $kernel $kernel */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(HeadsnetDoctrineToolsBundle::class);
        $kernel->addTestConfig(__DIR__ . '/Fixtures/config.yaml');
        $kernel->handleOptions($options);

        return $kernel;
    }

    #[Test]
    public function can_find_and_register_types(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $result = $container->getParameter('doctrine.dbal.connection_factory.types');

        $expected = [
            'dummy_string' => [
                'class' => 'Headsnet\DoctrineToolsBundle\_generated\HeadsnetDoctrineTools\Types\DummyStringObjectType',
            ],
            'dummy_integer' => [
                'class' => 'Headsnet\DoctrineToolsBundle\_generated\HeadsnetDoctrineTools\Types\DummyIntegerObjectType',
            ],
            'dummy_uuid' => [
                'class' => 'Headsnet\DoctrineToolsBundle\_generated\HeadsnetDoctrineTools\Types\DummyUuidObjectType',
            ],
        ];
        $this->assertEquals($expected, $result);
    }
}
