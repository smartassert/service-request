<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;

interface TypeDeserializerInterface
{
    /**
     * @param non-empty-string $class
     * @param array<mixed>     $data
     *
     * @throws ErrorValueTypeErrorException
     * @throws ErrorDeserializationException
     */
    public function deserialize(string $class, array $data): ?ErrorInterface;
}
