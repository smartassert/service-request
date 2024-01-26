<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

/**
 * @phpstan-import-type SerializedDuplicateObjectError from DuplicateObjectErrorInterface
 */
readonly class DuplicateObjectError extends Error implements DuplicateObjectErrorInterface, HasParameterInterface
{
    public function __construct(
        private ParameterInterface $field,
    ) {
        parent::__construct(DuplicateObjectErrorInterface::ERROR_CLASS);
    }

    public function getField(): ParameterInterface
    {
        return $this->field;
    }

    /**
     * @return SerializedDuplicateObjectError
     */
    public function serialize(): array
    {
        return [
            'class' => DuplicateObjectErrorInterface::ERROR_CLASS,
            'field' => $this->field->serialize(),
        ];
    }
}
