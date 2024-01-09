<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\StorageErrorInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\StorageErrorDataProviderTrait;

class StorageErrorTest extends TestCase
{
    use StorageErrorDataProviderTrait;

    /**
     * @dataProvider storageErrorDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(StorageErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }
}
