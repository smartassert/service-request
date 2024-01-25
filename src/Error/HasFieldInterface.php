<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Field\FieldInterface;

interface HasFieldInterface extends ErrorInterface
{
    public function getField(): FieldInterface;
}
