<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

interface HasParameterInterface extends ErrorInterface
{
    public function getParameter(): ParameterInterface;
}
