<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Deserializer\Parameter\Deserializer as ParameterDeserializer;
use SmartAssert\ServiceRequest\Exception\DeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;
use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

readonly class ErrorParameterDeserializer
{
    public function __construct(
        private ParameterDeserializer $parameterDeserializer,
    ) {
    }

    /**
     * @param array<mixed> $data
     *
     * @throws ErrorDeserializationException
     */
    public function deserialize(string $class, array $data): ParameterInterface
    {
        if (!array_key_exists('parameter', $data)) {
            throw new ErrorDeserializationException(
                $class,
                new DeserializationException('parameter', $data, DeserializationException::CODE_MISSING)
            );
        }

        $fieldData = $data['parameter'];
        if (!is_array($fieldData)) {
            throw new ErrorDeserializationException(
                $class,
                (new DeserializationException(
                    'parameter',
                    $data,
                    DeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('array', gettype($fieldData)))
            );
        }

        try {
            $field = $this->parameterDeserializer->deserialize($fieldData);
        } catch (\Throwable $fieldDeserializeException) {
            throw new ErrorDeserializationException(
                $class,
                new DeserializationException(
                    'parameter',
                    $data,
                    DeserializationException::CODE_INVALID,
                    $fieldDeserializeException,
                )
            );
        }

        return $field;
    }
}
