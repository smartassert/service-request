<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Error;

use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;

interface TypeDeserializerInterface
{
    /**
     * @param non-empty-string $class
     * @param array<mixed>     $data
     *
     * @throws ErrorDeserializationException
     */
    public function deserialize(string $class, array $data): ?ErrorInterface;
}
