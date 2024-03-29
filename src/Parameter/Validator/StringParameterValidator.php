<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter\Validator;

use SmartAssert\ServiceRequest\Exception\ErrorResponseException;
use SmartAssert\ServiceRequest\Exception\ErrorResponseExceptionFactory;
use SmartAssert\ServiceRequest\Parameter\ParameterInterface;
use SmartAssert\ServiceRequest\Parameter\SizeInterface;

readonly class StringParameterValidator
{
    public function __construct(
        private ErrorResponseExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws ErrorResponseException
     */
    public function validateString(ParameterInterface $parameter): string
    {
        $value = $parameter->getValue();
        if (!is_string($value)) {
            throw $this->exceptionFactory->createForBadRequest($parameter, 'wrong_type');
        }

        $sizeRequirements = $parameter->getRequirements()?->getSize();
        if (!$sizeRequirements instanceof SizeInterface) {
            return $value;
        }

        $size = mb_strlen($value);
        if (
            $size < $sizeRequirements->getMinimum()
            || (null !== $sizeRequirements->getMaximum() && $size > $sizeRequirements->getMaximum())
        ) {
            throw $this->exceptionFactory->createForBadRequest($parameter, 'wrong_size');
        }

        return $value;
    }

    /**
     * @return non-empty-string
     *
     * @throws ErrorResponseException
     */
    public function validateNonEmptyString(ParameterInterface $parameter): string
    {
        $value = $this->validateString($parameter);

        if ('' === $value) {
            throw $this->exceptionFactory->createForBadRequest($parameter, 'wrong_size');
        }

        return $value;
    }
}
