<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Deserializer\Field\Deserializer as FieldDeserializer;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;
use SmartAssert\ServiceRequest\Field\FieldInterface;

readonly class ErrorFieldDeserializer
{
    public function __construct(
        private FieldDeserializer $fieldDeserializer,
    ) {
    }

    /**
     * @param array<mixed> $data
     *
     * @throws ErrorDeserializationException
     * @throws ErrorValueTypeErrorException
     */
    public function deserialize(string $class, array $data): FieldInterface
    {
        if (!array_key_exists('field', $data)) {
            throw new ErrorDeserializationException(
                $class,
                'field',
                $data,
                ErrorDeserializationException::CODE_MISSING,
            );
        }

        $fieldData = $data['field'];
        if (!is_array($fieldData)) {
            throw new ErrorValueTypeErrorException($class, 'field', 'array', gettype($fieldData), $data);
        }

        try {
            $field = $this->fieldDeserializer->deserialize($fieldData);
        } catch (\Throwable $fieldDeserializeException) {
            throw new ErrorDeserializationException(
                $class,
                'field',
                $data,
                ErrorDeserializationException::CODE_INVALID,
                $fieldDeserializeException,
            );
        }

        return $field;
    }
}
