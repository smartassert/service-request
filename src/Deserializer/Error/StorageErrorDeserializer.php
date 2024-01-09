<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Error\StorageError;
use SmartAssert\ServiceRequest\Error\StorageErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;

readonly class StorageErrorDeserializer implements TypeDeserializerInterface
{
    public function deserialize(string $class, array $data): ?ErrorInterface
    {
        if (StorageErrorInterface::ERROR_CLASS !== $class) {
            return null;
        }

        $type = null;
        if (array_key_exists('type', $data)) {
            $type = $data['type'];
            if (!is_string($type) && null !== $type) {
                throw new ErrorValueTypeErrorException($class, 'type', 'string', gettype($type), $data);
            }

            $type = trim($type ?? '');
            if ('' === $type) {
                $type = null;
            }
        }

        if (!array_key_exists('object_type', $data)) {
            throw new ErrorValueMissingException($class, 'object_type', $data);
        }

        $objectType = $data['object_type'];
        if (!is_string($objectType)) {
            throw new ErrorValueTypeErrorException($class, 'object_type', 'string', gettype($objectType), $data);
        }

        $objectType = trim($objectType);
        if ('' === $objectType) {
            throw new ErrorValueEmptyException($class, 'object_type', $data);
        }

        $location = null;
        if (array_key_exists('location', $data)) {
            $location = $data['location'];
            if (!is_string($location) && null !== $location) {
                throw new ErrorValueTypeErrorException($class, 'location', 'string', gettype($location), $data);
            }

            $location = trim($location ?? '');
            if ('' === $location) {
                $location = null;
            }
        }

        $context = [];
        if (array_key_exists('context', $data)) {
            $context = $data['context'];
            if (!is_array($context)) {
                throw new ErrorValueTypeErrorException($class, 'context', 'array', gettype($context), $data);
            }
        }

        return new StorageError($type, $objectType, $location, $context);
    }
}
