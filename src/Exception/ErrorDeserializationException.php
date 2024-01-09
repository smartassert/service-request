<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Exception;

class ErrorDeserializationException extends \Exception
{
    public const CODE_MISSING = 1;
    public const CODE_EMPTY = 2;
    public const CODE_INVALID = 3;

    /**
     * @param array<mixed> $data
     * @param self::CODE_* $code
     */
    public function __construct(
        public readonly string $errorClass,
        public readonly string $name,
        public readonly array $data,
        int $code,
        public readonly ?ErrorContextInterface $context = null,
    ) {
        parent::__construct('', $code);
    }
}
