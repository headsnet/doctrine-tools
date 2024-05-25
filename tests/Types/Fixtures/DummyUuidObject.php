<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types\Fixtures;

use Headsnet\DoctrineToolsBundle\Attribute\DoctrineType;
use Symfony\Component\Uid\Uuid;

#[DoctrineType(name: 'dummy_uuid', type: 'uuid')]
class DummyUuidObject
{
    public function __construct(
        private readonly Uuid $value
    ) {
    }

    public static function create(Uuid $value): self
    {
        return new self($value);
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    public function asString(): string
    {
        return $this->value->toRfc4122();
    }
}
