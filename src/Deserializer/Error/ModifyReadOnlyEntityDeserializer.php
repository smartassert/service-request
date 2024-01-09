<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityError;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;

readonly class ModifyReadOnlyEntityDeserializer implements TypeDeserializerInterface
{
    public function deserialize(string $class, array $data): ?ErrorInterface
    {
        if (ModifyReadOnlyEntityErrorInterface::ERROR_CLASS !== $class) {
            return null;
        }

        if (!array_key_exists('entity', $data)) {
            throw new ErrorDeserializationException(
                $class,
                'entity',
                $data,
                ErrorDeserializationException::CODE_MISSING,
            );
        }

        $entityData = $data['entity'];
        if (!is_array($entityData)) {
            throw (new ErrorDeserializationException(
                $class,
                'entity',
                $data,
                ErrorDeserializationException::CODE_INVALID
            ))->withContext(new TypeErrorContext('array', gettype($entityData)));
        }

        if (!array_key_exists('id', $entityData)) {
            throw new ErrorDeserializationException(
                $class,
                'entity.id',
                $data,
                ErrorDeserializationException::CODE_MISSING
            );
        }

        $entityId = $entityData['id'];
        if (!is_string($entityId)) {
            throw (new ErrorDeserializationException(
                $class,
                'entity.id',
                $data,
                ErrorDeserializationException::CODE_INVALID
            ))->withContext(new TypeErrorContext('string', gettype($entityId)));
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
            throw new ErrorDeserializationException(
                $class,
                'entity.type',
                $data,
                ErrorDeserializationException::CODE_MISSING
            );
        }

        $entityType = $entityData['type'];
        if (!is_string($entityType)) {
            throw (new ErrorDeserializationException(
                $class,
                'entity.type',
                $data,
                ErrorDeserializationException::CODE_INVALID
            ))->withContext(new TypeErrorContext('string', gettype($entityType)));
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
