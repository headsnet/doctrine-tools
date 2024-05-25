<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types\StandardTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractIntegerMappingType extends Type
{
    /**
     * @codeCoverageIgnore
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    /**
     * @param int|null $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?object
    {
        if ($value === null) {
            return null;
        }

        $class = $this->getClass();

        return $class::create($value);
    }

    /**
     * @param object|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        if ($value === null) {
            return null;
        }

        return $value->asInteger(); // @phpstan-ignore-line
    }

    abstract public function getName(): string;

    abstract public function getClass(): string;
}
