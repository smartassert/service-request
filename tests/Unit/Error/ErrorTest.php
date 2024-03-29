<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\Error;
use SmartAssert\ServiceRequest\Error\ErrorInterface;

class ErrorTest extends TestCase
{
    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(ErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }

    /**
     * @return array<mixed>
     */
    public static function serializeDataProvider(): array
    {
        $class = md5((string) rand());
        $type = md5((string) rand());

        return [
            'class only' => [
                'error' => new Error($class, null),
                'serialized' => [
                    'class' => $class,
                ],
            ],
            'class and type' => [
                'error' => new Error($class, $type),
                'serialized' => [
                    'class' => $class,
                    'type' => $type,
                ],
            ],
        ];
    }
}
