<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

/**
 * @phpstan-import-type SerializedModifyReadOnlyEntityError from ModifyReadOnlyEntityErrorInterface
 */
readonly class ModifyReadOnlyEntityError extends Error implements ModifyReadOnlyEntityErrorInterface
{
    /**
     * @param non-empty-string $entityId
     * @param non-empty-string $entityType
     */
    public function __construct(
        private string $entityId,
        private string $entityType,
    ) {
        parent::__construct(ModifyReadOnlyEntityErrorInterface::ERROR_CLASS);
    }

    /**
     * @return SerializedModifyReadOnlyEntityError
     */
    public function serialize(): array
    {
        return [
            'class' => ModifyReadOnlyEntityErrorInterface::ERROR_CLASS,
            'entity' => [
                'id' => $this->entityId,
                'type' => $this->entityType,
            ],
        ];
    }
}
