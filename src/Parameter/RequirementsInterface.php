<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter;

interface RequirementsInterface
{
    /**
     * @return non-empty-string
     */
    public function getDataType(): string;

    public function getSize(): ?SizeInterface;
}
