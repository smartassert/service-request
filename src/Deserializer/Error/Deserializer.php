<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\DeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;
use SmartAssert\ServiceRequest\Exception\UnknownErrorClassException;

readonly class Deserializer
{
    /**
     * @var TypeDeserializerInterface[]
     */
    private array $typeDeserializers;

    /**
     * @param array<mixed> $typeDeserializers
     */
    public function __construct(
        array $typeDeserializers,
    ) {
        $filteredTypeDeserializers = [];

        foreach ($typeDeserializers as $item) {
            if ($item instanceof TypeDeserializerInterface) {
                $filteredTypeDeserializers[] = $item;
            }
        }

        $this->typeDeserializers = $filteredTypeDeserializers;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws UnknownErrorClassException
     * @throws ErrorDeserializationException
     */
    public function deserialize(array $data): ErrorInterface
    {
        if (!array_key_exists('class', $data)) {
            throw new ErrorDeserializationException(
                '',
                new DeserializationException('class', $data, DeserializationException::CODE_MISSING)
            );
        }

        $errorClass = $data['class'];
        if (!is_string($errorClass)) {
            throw new ErrorDeserializationException(
                '',
                (new DeserializationException(
                    'class',
                    $data,
                    DeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', gettype($errorClass)))
            );
        }

        if ('' === $errorClass) {
            throw new ErrorDeserializationException(
                '',
                new DeserializationException('class', $data, DeserializationException::CODE_EMPTY)
            );
        }

        foreach ($this->typeDeserializers as $typeDeserializer) {
            $error = $typeDeserializer->deserialize($errorClass, $data);

            if ($error instanceof ErrorInterface) {
                return $error;
            }
        }

        throw new UnknownErrorClassException($errorClass, $data);
    }
}
