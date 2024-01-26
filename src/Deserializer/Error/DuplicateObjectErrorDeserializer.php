<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\DuplicateObjectError;
use SmartAssert\ServiceRequest\Error\DuplicateObjectErrorInterface;
use SmartAssert\ServiceRequest\Error\ErrorInterface;

readonly class DuplicateObjectErrorDeserializer implements TypeDeserializerInterface
{
    public function __construct(
        private ErrorParameterDeserializer $errorParameterDeserializer,
    ) {
    }

    public function deserialize(string $class, array $data): ?ErrorInterface
    {
        if (DuplicateObjectErrorInterface::ERROR_CLASS !== $class) {
            return null;
        }

        return new DuplicateObjectError(
            $this->errorParameterDeserializer->deserialize($class, $data)
        );
    }
}
