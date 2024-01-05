<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class FieldValueInvalidException extends \Exception
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly string $name,
        public readonly array $data,
    ) {
        parent::__construct();
    }
}
