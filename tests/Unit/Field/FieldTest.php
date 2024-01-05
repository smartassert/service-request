<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Field;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Field\FieldInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class FieldTest extends TestCase
{
    use FieldDataProviderTrait;

    /**
     * @dataProvider fieldDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(FieldInterface $field, array $serialized): void
    {
        self::assertSame($serialized, $field->serialize());
    }
}
