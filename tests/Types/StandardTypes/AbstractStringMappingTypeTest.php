<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types\StandardTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Headsnet\DoctrineToolsBundle\Tests\Types\Fixtures\DummyStringObject;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\AbstractStringMappingType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractStringMappingType::class)]
class AbstractStringMappingTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('phpDataProvider')]
    public function converting_to_php_value(string|null $value, object|null $expectedResult): void
    {
        $sut = $this->buildSut();

        $result = $sut->convertToPHPValue(
            $value,
            $this->createMock(AbstractPlatform::class)
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: string|null, 1: DummyStringObject|null}>
     */
    public static function phpDataProvider(): iterable
    {
        yield ['foo', DummyStringObject::create('foo')];
        yield [null, null];
    }

    #[Test]
    #[DataProvider('databaseDataProvider')]
    public function converting_to_database_value(object|null $value, string|null $expectedResult): void
    {
        $sut = $this->buildSut();

        $result = $sut->convertToDatabaseValue(
            $value,
            $this->createMock(AbstractPlatform::class)
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: DummyStringObject|null, 1: string|null}>
     */
    public static function databaseDataProvider(): iterable
    {
        yield [DummyStringObject::create('foo'), 'foo'];
        yield [null, null];
    }

    private function buildSut(): AbstractStringMappingType
    {
        return new class() extends AbstractStringMappingType {
            public function getName(): string
            {
                return 'dummy';
            }

            public function getClass(): string
            {
                return DummyStringObject::class;
            }
        };
    }
}
