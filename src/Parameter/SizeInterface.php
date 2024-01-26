<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter;

interface SizeInterface
{
    public function getMinimum(): int;

    public function getMaximum(): ?int;
}
