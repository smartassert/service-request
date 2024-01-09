<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class ErrorValueTypeErrorException extends \Exception
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly ?string $errorClass,
        public readonly string $name,
        public readonly string $expected,
        public readonly string $actual,
        public readonly array $data,
    ) {
        parent::__construct();
    }
}
