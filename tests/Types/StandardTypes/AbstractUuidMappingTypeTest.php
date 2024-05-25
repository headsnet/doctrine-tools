<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types\StandardTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Headsnet\DoctrineToolsBundle\Tests\Types\Fixtures\DummyUuidObject;
use Headsnet\DoctrineToolsBundle\Types\StandardTypes\AbstractUuidMappingType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(AbstractUuidMappingType::class)]
class AbstractUuidMappingTypeTest extends TestCase
{
    #[Test]
    #[DataProvider('phpDataProvider')]
    public function converting_to_php_value(Uuid|string|null $value, object|null $expectedResult): void
    {
        $sut = $this->buildSut();

        $result = $sut->convertToPHPValue(
            $value,
            $this->createMock(AbstractPlatform::class)
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: Uuid|string|null, 1: DummyUuidObject|null}>
     */
    public static function phpDataProvider(): iterable
    {
        $uuidV7 = Uuid::v7();
        yield [$uuidV7, DummyUuidObject::create($uuidV7)];
        yield [$uuidV7->toRfc4122(), DummyUuidObject::create($uuidV7)];
        yield [null, null];
    }

    #[Test]
    #[DataProvider('databaseDataProvider')]
    public function converting_to_database_value(object|string|null $value, Uuid|null $expectedResult): void
    {
        $sut = $this->buildSut();

        $result = $sut->convertToDatabaseValue(
            $value,
            $this->createMock(AbstractPlatform::class)
        );

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{0: DummyUuidObject|string|null, 1: Uuid|string|null}>
     */
    public static function databaseDataProvider(): iterable
    {
        $value = Uuid::v7();
        yield [DummyUuidObject::create($value), $value];
        yield [DummyUuidObject::create($value)->asString(), $value];
        yield [null, null];
    }

    private function buildSut(): AbstractUuidMappingType
    {
        return new class() extends AbstractUuidMappingType {
            public function getName(): string
            {
                return 'dummy';
            }

            public function getClass(): string
            {
                return DummyUuidObject::class;
            }
        };
    }
}
