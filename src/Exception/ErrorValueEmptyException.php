<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class ErrorValueEmptyException extends \Exception
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly ?string $errorClass,
        public readonly string $name,
        public readonly array $data
    ) {
        parent::__construct();
    }
}
