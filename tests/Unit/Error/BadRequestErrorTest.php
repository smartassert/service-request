<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\BadRequestErrorDataProvider;

class BadRequestErrorTest extends TestCase
{
    use BadRequestErrorDataProvider;

    /**
     * @dataProvider badRequestErrorDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(BadRequestErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }
}
