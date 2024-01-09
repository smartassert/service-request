<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityErrorInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\ModifyReadOnlyEntityErrorTrait;

class ModifyReadOnlyEntityErrorTest extends TestCase
{
    use ModifyReadOnlyEntityErrorTrait;

    /**
     * @dataProvider modifyReadOnlyEntityDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(ModifyReadOnlyEntityErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }
}
