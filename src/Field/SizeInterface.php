<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Field;

interface SizeInterface
{
    public function getMinimum(): int;

    public function getMaximum(): ?int;
}
