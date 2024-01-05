<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

/**
 * @phpstan-import-type SerializedStorageError from StorageErrorInterface
 */
readonly class StorageError extends Error implements StorageErrorInterface
{
    /**
     * @param ?non-empty-string     $type
     * @param non-empty-string      $objectType
     * @param ?non-empty-string     $location
     * @param array<string, scalar> $context
     */
    public function __construct(
        ?string $type,
        private string $objectType,
        private ?string $location,
        private array $context,
    ) {
        parent::__construct(StorageErrorInterface::ERROR_CLASS, $type);
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return SerializedStorageError
     */
    public function serialize(): array
    {
        return [
            'class' => StorageErrorInterface::ERROR_CLASS,
            'type' => $this->getType(),
            'location' => $this->getLocation(),
            'object_type' => $this->getObjectType(),
            'context' => $this->getContext(),
        ];
    }
}
