<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\StorageError;
use SmartAssert\ServiceRequest\Error\StorageErrorInterface;

class StorageErrorTest extends TestCase
{
    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(StorageErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }

    /**
     * @return array<mixed>
     */
    public static function serializeDataProvider(): array
    {
        $type = md5((string) rand());
        $objectType = md5((string) rand());
        $location = md5((string) rand());

        $contextItems = rand(0, 10);
        $context = [];
        for ($i = 0; $i <= $contextItems; ++$i) {
            $context[md5((string) rand())] = md5((string) rand());
        }

        // ?type
        // object type
        // ?location
        // context (array)

        return [
            'no type, no location, empty context' => [
                'error' => new StorageError(null, $objectType, null, []),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => null,
                    'location' => null,
                    'object_type' => $objectType,
                    'context' => [],
                ],
            ],
            'type, no location, empty context' => [
                'error' => new StorageError($type, $objectType, null, []),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => $type,
                    'location' => null,
                    'object_type' => $objectType,
                    'context' => [],
                ],
            ],
            'no type, location, empty context' => [
                'error' => new StorageError(null, $objectType, $location, []),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => null,
                    'location' => $location,
                    'object_type' => $objectType,
                    'context' => [],
                ],
            ],
            'no type, no location, context' => [
                'error' => new StorageError(null, $objectType, null, $context),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => null,
                    'location' => null,
                    'object_type' => $objectType,
                    'context' => $context,
                ],
            ],
            'type, location, context' => [
                'error' => new StorageError($type, $objectType, $location, $context),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => $type,
                    'location' => $location,
                    'object_type' => $objectType,
                    'context' => $context,
                ],
            ],
        ];
    }
}
