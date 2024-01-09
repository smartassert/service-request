<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Error\StorageError;
use SmartAssert\ServiceRequest\Error\StorageErrorInterface;
use SmartAssert\ServiceRequest\Exception\DeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;

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
                throw new ErrorDeserializationException(
                    $class,
                    (new DeserializationException(
                        'type',
                        $data,
                        DeserializationException::CODE_INVALID
                    ))->withContext(new TypeErrorContext('string', gettype($type)))
                );
            }

            $type = trim($type ?? '');
            if ('' === $type) {
                $type = null;
            }
        }

        if (!array_key_exists('object_type', $data)) {
            throw new ErrorDeserializationException(
                $class,
                new DeserializationException('object_type', $data, DeserializationException::CODE_MISSING)
            );
        }

        $objectType = $data['object_type'];
        if (!is_string($objectType)) {
            throw new ErrorDeserializationException(
                $class,
                (new DeserializationException(
                    'object_type',
                    $data,
                    DeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', gettype($objectType)))
            );
        }

        $objectType = trim($objectType);
        if ('' === $objectType) {
            throw new ErrorDeserializationException(
                $class,
                new DeserializationException('object_type', $data, DeserializationException::CODE_EMPTY)
            );
        }

        $location = null;
        if (array_key_exists('location', $data)) {
            $location = $data['location'];
            if (!is_string($location) && null !== $location) {
                throw new ErrorDeserializationException(
                    $class,
                    (new DeserializationException(
                        'location',
                        $data,
                        DeserializationException::CODE_INVALID
                    ))->withContext(new TypeErrorContext('string', gettype($location)))
                );
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
                throw new ErrorDeserializationException(
                    $class,
                    (new DeserializationException(
                        'context',
                        $data,
                        DeserializationException::CODE_INVALID
                    ))->withContext(new TypeErrorContext('array', gettype($context)))
                );
            }
        }

        return new StorageError($type, $objectType, $location, $context);
    }
}
