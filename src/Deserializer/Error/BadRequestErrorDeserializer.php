<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\DeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;

readonly class BadRequestErrorDeserializer implements TypeDeserializerInterface
{
    public function __construct(
        private ErrorParameterDeserializer $errorFieldDeserializer,
    ) {
    }

    public function deserialize(string $class, array $data): ?ErrorInterface
    {
        if (BadRequestErrorInterface::ERROR_CLASS !== $class) {
            return null;
        }

        if (!array_key_exists('type', $data)) {
            throw new ErrorDeserializationException(
                $class,
                new DeserializationException('type', $data, DeserializationException::CODE_MISSING)
            );
        }

        $type = $data['type'];
        if (!is_string($type)) {
            throw new ErrorDeserializationException(
                $class,
                (new DeserializationException(
                    'type',
                    $data,
                    DeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', gettype($type)))
            );
        }

        $type = trim($type);
        if ('' === $type) {
            throw new ErrorDeserializationException(
                $class,
                new DeserializationException('type', $data, DeserializationException::CODE_EMPTY)
            );
        }

        return new BadRequestError(
            $this->errorFieldDeserializer->deserialize($class, $data),
            $type
        );
    }
}
