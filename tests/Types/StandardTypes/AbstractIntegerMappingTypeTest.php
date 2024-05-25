<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types\StandardTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Headsnet\DoctrineToolsBundle\Tests\Types\Fixtures\DummyIntegerObject;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\AbstractIntegerMappingType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractIntegerMappingType::class)]
class AbstractIntegerMappingTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('phpDataProvider')]
    public function converting_to_php_value(int|null $value, object|null $expectedResult): void
    {
        $sut = $this->buildSut();

        $result = $sut->convertToPHPValue(
            $value,
            $this->createMock(AbstractPlatform::class)
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: int|null, 1: DummyIntegerObject|null}>
     */
    public static function phpDataProvider(): iterable
    {
        yield [42, DummyIntegerObject::create(42)];
        yield [null, null];
    }

    #[Test]
    #[DataProvider('databaseDataProvider')]
    public function converting_to_database_value(object|null $value, int|null $expectedResult): void
    {
        $sut = $this->buildSut();

        $result = $sut->convertToDatabaseValue(
            $value,
            $this->createMock(AbstractPlatform::class)
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: DummyIntegerObject|null, 1: int|null}>
     */
    public static function databaseDataProvider(): iterable
    {
        yield [DummyIntegerObject::create(42), 42];
        yield [null, null];
    }

    private function buildSut(): AbstractIntegerMappingType
    {
        return new class() extends AbstractIntegerMappingType {
            public function getName(): string
            {
                return 'dummy';
            }

            public function getClass(): string
            {
                return DummyIntegerObject::class;
            }
        };
    }
}
