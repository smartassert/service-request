<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueInvalidException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;
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
     * @throws ErrorValueEmptyException
     * @throws ErrorValueInvalidException
     * @throws ErrorValueMissingException
     * @throws ErrorValueTypeErrorException
     * @throws UnknownErrorClassException
     * @throws ErrorDeserializationException
     */
    public function deserialize(array $data): ErrorInterface
    {
        if (!array_key_exists('class', $data)) {
            throw new ErrorDeserializationException('', 'class', $data, ErrorDeserializationException::CODE_MISSING);
        }

        $errorClass = $data['class'];
        if (!is_string($errorClass)) {
            throw new ErrorDeserializationException(
                '',
                'class',
                $data,
                ErrorDeserializationException::CODE_INVALID,
                new TypeErrorContext('string', gettype($errorClass))
            );
        }

        if ('' === $errorClass) {
            throw new ErrorDeserializationException('', 'class', $data, ErrorDeserializationException::CODE_EMPTY);
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
