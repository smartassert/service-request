<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter;

readonly class Factory
{
    /**
     * @param non-empty-string $name
     */
    public function createStringParameter(
        string $name,
        string $value,
        int $minimumLength,
        ?int $maximumLength
    ): ParameterInterface {
        return (new Parameter($name, $value))
            ->withRequirements(new Requirements('string', new Size($minimumLength, $maximumLength)))
        ;
    }
}
