<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Parameter;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Parameter\ParameterInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class ParameterTest extends TestCase
{
    use FieldDataProviderTrait;

    /**
     * @dataProvider fieldDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(ParameterInterface $field, array $serialized): void
    {
        self::assertSame($serialized, $field->serialize());
    }
}
