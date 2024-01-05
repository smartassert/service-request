<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityError;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityErrorInterface;

class ModifyReadOnlyEntityErrorTest extends TestCase
{
    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(ModifyReadOnlyEntityErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }

    /**
     * @return array<mixed>
     */
    public static function serializeDataProvider(): array
    {
        $entityId = md5((string) rand());
        $entityType = md5((string) rand());

        return [
            'class only' => [
                'error' => new ModifyReadOnlyEntityError($entityId, $entityType),
                'serialized' => [
                    'class' => ModifyReadOnlyEntityErrorInterface::ERROR_CLASS,
                    'entity' => [
                        'id' => $entityId,
                        'type' => $entityType,
                    ],
                ],
            ],
        ];
    }
}
