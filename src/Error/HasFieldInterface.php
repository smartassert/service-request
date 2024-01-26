<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

interface HasFieldInterface extends ErrorInterface
{
    public function getField(): ParameterInterface;
}
