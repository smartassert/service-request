<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter;

/**
 * @phpstan-type SerializedParameter array{
 *   name: non-empty-string,
 *   value: scalar|array<scalar>,
 *   requirements?: array{
 *     data_type: string,
 *     size?: array{
 *       minimum: int,
 *       maximum: ?int
 *     }
 *   }
 * }
 */
interface ParameterInterface
{
    /**
     * @return non-empty-string
     */
    public function getName(): string;

    /**
     * @return array<scalar>|scalar
     */
    public function getValue(): mixed;

    public function getRequirements(): ?RequirementsInterface;

    public function withRequirements(RequirementsInterface $requirements): ParameterInterface;

    public function getErrorPosition(): ?int;

    public function withErrorPosition(int $position): ParameterInterface;

    /**
     * @return SerializedParameter
     */
    public function serialize(): array;
}
