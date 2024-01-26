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
        private ParameterInterface $parameter,
    ) {
        parent::__construct(DuplicateObjectErrorInterface::ERROR_CLASS);
    }

    public function getParameter(): ParameterInterface
    {
        return $this->parameter;
    }

    /**
     * @return SerializedDuplicateObjectError
     */
    public function serialize(): array
    {
        return [
            'class' => DuplicateObjectErrorInterface::ERROR_CLASS,
            'field' => $this->parameter->serialize(),
        ];
    }
}
