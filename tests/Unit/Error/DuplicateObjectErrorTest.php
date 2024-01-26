<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\DuplicateObjectError;
use SmartAssert\ServiceRequest\Error\DuplicateObjectErrorInterface;
use SmartAssert\ServiceRequest\Error\HasFieldInterface;
use SmartAssert\ServiceRequest\Parameter\Field;
use SmartAssert\ServiceRequest\Tests\DataProvider\DuplicateObjectErrorDataProvider;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class DuplicateObjectErrorTest extends TestCase
{
    use FieldDataProviderTrait;
    use DuplicateObjectErrorDataProvider;

    /**
     * @dataProvider duplicateObjectErrorDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(DuplicateObjectErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }

    public function testHasField(): void
    {
        self::assertInstanceOf(
            HasFieldInterface::class,
            new DuplicateObjectError(new Field('name', 'value'))
        );
    }
}
