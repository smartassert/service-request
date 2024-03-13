<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter\Validator;

use SmartAssert\ServiceRequest\Exception\ErrorResponseException;
use SmartAssert\ServiceRequest\Exception\ErrorResponseExceptionFactory;
use SmartAssert\ServiceRequest\Parameter\ParameterInterface;
use SmartAssert\ServiceRequest\Parameter\SizeInterface;

readonly class PositiveIntegerParameterValidator
{
    public function __construct(
        private ErrorResponseExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @return positive-int
     *
     * @throws ErrorResponseException
     */
    public function validateInteger(ParameterInterface $parameter): int
    {
        $value = $parameter->getValue();
        if (!is_int($value)) {
            throw $this->exceptionFactory->createForBadRequest($parameter, 'wrong_type');
        }

        if ($value < 1) {
            throw $this->exceptionFactory->createForBadRequest($parameter, 'wrong_size');
        }

        $sizeRequirements = $parameter->getRequirements()?->getSize();
        if (!$sizeRequirements instanceof SizeInterface) {
            return $value;
        }

        if (
            $value < $sizeRequirements->getMinimum()
            || (null !== $sizeRequirements->getMaximum() && $value > $sizeRequirements->getMaximum())
        ) {
            throw $this->exceptionFactory->createForBadRequest($parameter, 'wrong_size');
        }

        return $value;
    }
}
