<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

use SmartAssert\ServiceRequest\Field\FieldInterface;

/**
 * @phpstan-import-type SerializedDuplicateObjectError from DuplicateObjectErrorInterface
 */
readonly class DuplicateObjectError extends Error implements DuplicateObjectErrorInterface
{
    public function __construct(
        private FieldInterface $field,
    ) {
        parent::__construct(DuplicateObjectErrorInterface::ERROR_CLASS);
    }

    public function getField(): FieldInterface
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
