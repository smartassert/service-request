<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Parameter\ParameterInterface;

/**
 * @phpstan-import-type SerializedBadRequest from BadRequestErrorInterface
 */
readonly class BadRequestError extends Error implements BadRequestErrorInterface, HasParameterInterface
{
    /**
     * @param non-empty-string $errorType
     */
    public function __construct(
        private ParameterInterface $parameter,
        private string $errorType,
    ) {
        parent::__construct(BadRequestErrorInterface::ERROR_CLASS, $errorType);
    }

    public function getParameter(): ParameterInterface
    {
        return $this->parameter;
    }

    /**
     * @return SerializedBadRequest
     */
    public function serialize(): array
    {
        return [
            'class' => BadRequestErrorInterface::ERROR_CLASS,
            'type' => $this->errorType,
            'parameter' => $this->parameter->serialize(),
        ];
    }
}
