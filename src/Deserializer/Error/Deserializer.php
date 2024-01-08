<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;
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
     * @throws ErrorValueTypeErrorException
     * @throws UnknownErrorClassException
     * @throws ErrorValueMissingException
     */
    public function deserialize(array $data): ErrorInterface
    {
        if (!array_key_exists('class', $data)) {
            throw new ErrorValueMissingException(null, 'class', $data);
        }

        $errorClass = $data['class'];
        if (!is_string($errorClass)) {
            throw new ErrorValueTypeErrorException(
                null,
                'class',
                'string',
                gettype($errorClass),
                $data,
            );
        }

        if ('' === $errorClass) {
            throw new ErrorValueEmptyException(null, 'class', $data);
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