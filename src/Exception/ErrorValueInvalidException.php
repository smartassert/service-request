<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class ErrorValueInvalidException extends \Exception
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly ?string $errorClass,
        public readonly string $name,
        public readonly array $data,
        public readonly ?\Throwable $previousException = null
    ) {
        parent::__construct('', 0, $previousException);
    }
}
