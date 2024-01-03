<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class InvalidFieldDataException extends \Exception
{
    public const CODE_NAME_MISSING = 1;
    public const CODE_VALUE_NOT_SCALAR = 2;
    public const CODE_DATA_TYPE_EMPTY = 3;
    public const CODE_SIZE_MINIMUM_NOT_AN_INTEGER = 4;

    public const CODE_VALUE_MISSING = 5;

    /**
     * @param array<mixed> $data
     * @param self::CODE_* $code
     */
    public function __construct(
        public readonly array $data,
        int $code,
    ) {
        parent::__construct('', $code);
    }
}
