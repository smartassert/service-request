<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\DataProvider;

use SmartAssert\ServiceRequest\Error\StorageError;
use SmartAssert\ServiceRequest\Error\StorageErrorInterface;

trait StorageErrorDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function storageErrorDataProvider(): array
    {
        $type = md5((string) rand());
        $objectType = md5((string) rand());
        $location = md5((string) rand());

        $contextItems = rand(0, 10);
        $context = [];
        for ($i = 0; $i <= $contextItems; ++$i) {
            $context[md5((string) rand())] = md5((string) rand());
        }

        return [
            'storage error: no type, no location, empty context' => [
                'error' => new StorageError(null, $objectType, null, []),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => null,
                    'location' => null,
                    'object_type' => $objectType,
                    'context' => [],
                ],
            ],
            'storage error: type, no location, empty context' => [
                'error' => new StorageError($type, $objectType, null, []),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => $type,
                    'location' => null,
                    'object_type' => $objectType,
                    'context' => [],
                ],
            ],
            'storage error: no type, location, empty context' => [
                'error' => new StorageError(null, $objectType, $location, []),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => null,
                    'location' => $location,
                    'object_type' => $objectType,
                    'context' => [],
                ],
            ],
            'storage error: no type, no location, context' => [
                'error' => new StorageError(null, $objectType, null, $context),
                'serialized' => [
                    'class' => StorageErrorInterface::ERROR_CLASS,
                    'type' => null,
                    'location' => null,
                    'object_type' => $objectType,
                    'context' => $context,
                ],
            ],
            'storage error: type, location, context' => [
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
