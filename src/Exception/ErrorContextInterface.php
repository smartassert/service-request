<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

interface ErrorContextInterface
{
    /**
     * @return array<mixed>
     */
    public function get(): array;
}
