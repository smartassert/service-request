<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class UnspecifiedErrorClassException extends \Exception
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(
        public readonly array $data
    ) {
        parent::__construct();
    }
}