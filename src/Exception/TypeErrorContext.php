<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

readonly class TypeErrorContext implements ErrorContextInterface
{
    public function __construct(
        private string $expected,
        private string $actual,
    ) {
    }

    /**
     * @return array{expected: string, actual: string}
     */
    public function get(): array
    {
        return [
            'expected' => $this->expected,
            'actual' => $this->actual,
        ];
    }
}
