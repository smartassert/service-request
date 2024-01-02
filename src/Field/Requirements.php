<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Field;

readonly class Requirements implements RequirementsInterface
{
    /**
     * @param non-empty-string $dataType
     */
    public function __construct(
        private string $dataType,
        private ?SizeInterface $size = null,
    ) {
    }

    public function getDataType(): string
    {
        return $this->dataType;
    }

    public function getSize(): ?SizeInterface
    {
        return $this->size;
    }
}
