<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Types;

final class CandidateType
{
    private string $baseTypeClass = '';

    /**
     * @param class-string $objectClass
     */
    public function __construct(
        public readonly string $typeName,
        public readonly string $typeClass,
        public readonly string $baseType,
        public readonly string $objectClass,
    ) {
    }

    public function setBaseTypeClass(string $baseTypeClass): void
    {
        $this->baseTypeClass = $baseTypeClass;
    }

    public function getBaseTypeClass(): string
    {
        return $this->baseTypeClass;
    }
}
