<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

interface StorageExceptionInterface extends \Throwable
{
    /**
     * @return non-empty-string
     */
    public function getObjectType(): string;

    /**
     * @return ?non-empty-string
     */
    public function getOperation(): ?string;

    /**
     * @return ?non-empty-string
     */
    public function getLocation(): ?string;

    /**
     * @return array<string, scalar>
     */
    public function getContext(): array;
}
