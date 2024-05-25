<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types\StandardTypes;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(tags: [MappingPrototype::TAG])]
class UuidMappingPrototype implements MappingPrototype
{
    public static function supports(string $type): bool
    {
        return $type === 'uuid';
    }

    public static function mappedBy(): string
    {
        return AbstractUuidMappingType::class;
    }
}
