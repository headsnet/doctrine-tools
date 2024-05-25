<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types\StandardTypes;

interface MappingPrototype
{
    public const TAG = 'headsnet_doctrine_tools.standard_type';

    public static function supports(string $type): bool;

    public static function mappedBy(): string;
}
