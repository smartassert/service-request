<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueInvalidException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;

interface TypeDeserializerInterface
{
    /**
     * @param non-empty-string $class
     * @param array<mixed>     $data
     *
     * @throws ErrorValueMissingException
     * @throws ErrorValueEmptyException
     * @throws ErrorValueTypeErrorException
     * @throws ErrorValueInvalidException
     */
    public function deserialize(string $class, array $data): ?ErrorInterface;
}
