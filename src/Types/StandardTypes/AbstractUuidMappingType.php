<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types\StandardTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Uid\Uuid;

abstract class AbstractUuidMappingType extends Type
{
    /**
     * @codeCoverageIgnore
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    /**
     * @param Uuid|string|null $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?object
    {
        if ($value === null) {
            return null;
        }

        $class = $this->getClass();

        if (is_string($value)) {
            return $class::fromString($value);
        }

        return $class::create($value);
    }

    /**
     * @param object|string|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        return $value->asString(); // @phpstan-ignore-line
    }

    abstract public function getName(): string;

    abstract public function getClass(): string;
}
