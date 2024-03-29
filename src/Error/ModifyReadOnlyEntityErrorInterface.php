<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Error;

/**
 * @phpstan-type SerializedModifyReadOnlyEntityError array{
 *    class: 'modify_read_only',
 *    entity: array{
 *      id: non-empty-string,
 *      type: non-empty-string
 *    }
 *  }
 */
interface ModifyReadOnlyEntityErrorInterface extends ErrorInterface
{
    public const ERROR_CLASS = 'modify_read_only';

    /**
     * @return SerializedModifyReadOnlyEntityError
     */
    public function serialize(): array;
}
