<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

/**
 * @phpstan-import-type SerializedField from ParameterInterface
 *
 * @phpstan-type SerializedDuplicateObjectError array{class: 'duplicate', field: SerializedField}
 */
interface DuplicateObjectErrorInterface extends ErrorInterface
{
    public const ERROR_CLASS = 'duplicate';

    public function getField(): ParameterInterface;

    /**
     * @return SerializedDuplicateObjectError
     */
    public function serialize(): array;
}
