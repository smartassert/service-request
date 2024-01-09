<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\DataProvider;

use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityError;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityErrorInterface;

trait ModifyReadOnlyEntityErrorTrait
{
    /**
     * @return array<mixed>
     */
    public static function modifyReadOnlyEntityDataProvider(): array
    {
        $entityId = md5((string) rand());
        $entityType = md5((string) rand());

        return [
            'modify read-only entity' => [
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
