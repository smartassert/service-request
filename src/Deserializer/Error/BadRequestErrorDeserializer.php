<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Deserializer\Field\Deserializer as FieldDeserializer;
use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueInvalidException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;

readonly class BadRequestErrorDeserializer implements TypeDeserializerInterface
{
    public function __construct(
        private FieldDeserializer $fieldDeserializer,
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

        if (!array_key_exists('field', $data)) {
            throw new ErrorValueMissingException($class, 'field', $data);
        }

        $fieldData = $data['field'];
        if (!is_array($fieldData)) {
            throw new ErrorValueTypeErrorException($class, 'field', 'array', gettype($fieldData), $data);
        }

        try {
            $field = $this->fieldDeserializer->deserialize($fieldData);
        } catch (\Throwable $fieldDeserializeException) {
            throw new ErrorValueInvalidException($class, 'field', $data, $fieldDeserializeException);
        }

        return new BadRequestError($field, $type);
    }
}
