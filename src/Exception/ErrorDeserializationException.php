<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class ErrorDeserializationException extends \Exception
{
    public function __construct(
        public readonly string $errorClass,
        public readonly DeserializationException $deserializationException,
    ) {
        parent::__construct('', $this->deserializationException->code);
    }
}
