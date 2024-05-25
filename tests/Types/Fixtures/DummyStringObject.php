<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Types\Fixtures;

use Headsnet\DoctrineToolsBundle\Attribute\DoctrineType;

#[DoctrineType(name: 'dummy_string', type: 'string')]
class DummyStringObject
{
    public function __construct(
        private readonly string $value
    ) {
    }

    public static function create(string $value): self
    {
        return new self($value);
    }

    public function asString(): string
    {
        return $this->value;
    }
}
