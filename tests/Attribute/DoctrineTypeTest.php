<?php
declare(strict_types=1);

namespace Headsnet\DoctrineToolsBundle\Tests\Attribute;

use Headsnet\DoctrineToolsBundle\Attribute\DoctrineType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DoctrineType::class)]
class DoctrineTypeTest extends TestCase
{
    #[Test]
    public function name_can_be_specified(): void
    {
        $sut = new DoctrineType(
            name: 'custom_name',
            type: 'string',
        );

        $this->assertEquals('custom_name', $sut->name);
    }
}
