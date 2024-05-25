<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types\StandardTypes;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [MappingPrototype::TAG])]
class StringMappingPrototype implements MappingPrototype
{
    public static function supports(string $type): bool
    {
        return $type === 'string';
    }

    public static function mappedBy(): string
    {
        return AbstractStringMappingType::class;
    }
}
