<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

/**
 * @phpstan-import-type SerializedParameter from ParameterInterface
 *
 * @phpstan-type SerializedDuplicateObjectError array{class: 'duplicate', parameter: SerializedParameter}
 */
interface DuplicateObjectErrorInterface extends ErrorInterface
{
    public const ERROR_CLASS = 'duplicate';

    public function getParameter(): ParameterInterface;

    /**
     * @return SerializedDuplicateObjectError
     */
    public function serialize(): array;
}
