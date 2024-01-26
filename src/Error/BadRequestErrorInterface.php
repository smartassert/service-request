<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

/**
 * @phpstan-import-type SerializedField from ParameterInterface
 *
 * @phpstan-type SerializedBadRequest array{class: 'bad_request', type: non-empty-string, field: SerializedField}
 */
interface BadRequestErrorInterface extends ErrorInterface
{
    public const ERROR_CLASS = 'bad_request';

    public function getField(): ParameterInterface;

    /**
     * @return SerializedBadRequest
     */
    public function serialize(): array;
}
