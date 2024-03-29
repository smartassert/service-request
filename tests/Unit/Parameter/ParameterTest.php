<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Parameter;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Parameter\ParameterInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\ParameterDataProviderTrait;

class ParameterTest extends TestCase
{
    use ParameterDataProviderTrait;

    /**
     * @dataProvider parameterDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(ParameterInterface $parameter, array $serialized): void
    {
        self::assertSame($serialized, $parameter->serialize());
    }
}
