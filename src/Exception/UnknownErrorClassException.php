<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class UnknownErrorClassException extends \Exception
{
    /**
     * @param non-empty-string $class
     * @param array<mixed>     $data
     */
    public function __construct(
        public readonly string $class,
        public readonly array $data
    ) {
        parent::__construct();
    }
}
