<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class DoctrineType
{
    public function __construct(
        public readonly string $name,
        public readonly string $type
    ) {
    }
}
