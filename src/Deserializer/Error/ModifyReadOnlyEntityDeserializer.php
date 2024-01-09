<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityError;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;

readonly class ModifyReadOnlyEntityDeserializer implements TypeDeserializerInterface
{
    public function deserialize(string $class, array $data): ?ErrorInterface
    {
        if (ModifyReadOnlyEntityErrorInterface::ERROR_CLASS !== $class) {
            return null;
        }

        if (!array_key_exists('entity', $data)) {
            throw new ErrorValueMissingException($class, 'entity', $data);
        }

        $entityData = $data['entity'];
        if (!is_array($entityData)) {
            throw new ErrorValueTypeErrorException($class, 'entity', 'array', gettype($entityData), $data);
        }

        if (!array_key_exists('id', $entityData)) {
            throw new ErrorValueMissingException($class, 'entity.id', $data);
        }

        $entityId = $entityData['id'];
        if (!is_string($entityId)) {
            throw new ErrorValueTypeErrorException($class, 'entity.id', 'string', gettype($entityId), $data);
        }

        $entityId = trim($entityId);
        if ('' === $entityId) {
            throw new ErrorDeserializationException(
                $class,
                'entity.id',
                $data,
                ErrorDeserializationException::CODE_EMPTY,
            );
        }

        if (!array_key_exists('type', $entityData)) {
            throw new ErrorValueMissingException($class, 'entity.type', $data);
        }

        $entityType = $entityData['type'];
        if (!is_string($entityType)) {
            throw new ErrorValueTypeErrorException($class, 'entity.type', 'string', gettype($entityType), $data);
        }

        $entityType = trim($entityType);
        if ('' === $entityType) {
            throw new ErrorDeserializationException(
                $class,
                'entity.type',
                $data,
                ErrorDeserializationException::CODE_EMPTY,
            );
        }

        return new ModifyReadOnlyEntityError($entityId, $entityType);
    }
}
