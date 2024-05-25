<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types\Fixtures;

use Headsnet\DoctrineToolsBundle\Attribute\DoctrineType;

#[DoctrineType(name: 'dummy_integer', type: 'integer')]
class DummyIntegerObject
{
    public function __construct(
        private readonly int $value
    ) {
    }

    public static function create(int $value): self
    {
        return new self($value);
    }

    public function asInteger(): int
    {
        return $this->value;
    }
}
