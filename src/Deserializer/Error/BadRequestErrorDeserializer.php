<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;

readonly class BadRequestErrorDeserializer implements TypeDeserializerInterface
{
    public function __construct(
        private ErrorFieldDeserializer $errorFieldDeserializer,
    ) {
    }

    public function deserialize(string $class, array $data): ?ErrorInterface
    {
        if (BadRequestErrorInterface::ERROR_CLASS !== $class) {
            return null;
        }

        if (!array_key_exists('type', $data)) {
            throw new ErrorValueMissingException($class, 'type', $data);
        }

        $type = $data['type'];
        if (!is_string($type)) {
            throw new ErrorValueTypeErrorException($class, 'type', 'string', gettype($type), $data);
        }

        $type = trim($type);
        if ('' === $type) {
            throw new ErrorValueEmptyException($class, 'type', $data);
        }

        return new BadRequestError(
            $this->errorFieldDeserializer->deserialize($class, $data),
            $type
        );
    }
}
