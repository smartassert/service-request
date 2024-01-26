<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Parameter;

class Field implements FieldInterface
{
    private ?int $errorPosition = null;
    private ?RequirementsInterface $requirements = null;

    /**
     * @param non-empty-string     $name
     * @param array<scalar>|scalar $value
     */
    public function __construct(
        private readonly string $name,
        private readonly array|bool|float|int|string $value,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): array|bool|float|int|string
    {
        return $this->value;
    }

    public function getRequirements(): ?RequirementsInterface
    {
        return $this->requirements;
    }

    public function withRequirements(RequirementsInterface $requirements): FieldInterface
    {
        $new = clone $this;
        $new->requirements = $requirements;

        return $new;
    }

    public function getErrorPosition(): ?int
    {
        return $this->errorPosition;
    }

    public function withErrorPosition(int $position): FieldInterface
    {
        $new = clone $this;
        $new->errorPosition = $position;

        return $new;
    }

    public function serialize(): array
    {
        $data = [
            'name' => $this->getName(),
            'value' => $this->getValue(),
        ];

        if (null !== $this->errorPosition) {
            $data['position'] = $this->errorPosition;
        }

        if ($this->requirements instanceof RequirementsInterface) {
            $requirementsData = [
                'data_type' => $this->requirements->getDataType(),
            ];

            $size = $this->requirements->getSize();
            if ($size instanceof SizeInterface) {
                $requirementsData['size'] = ['minimum' => $size->getMinimum(), 'maximum' => $size->getMaximum()];
            }

            $data['requirements'] = $requirementsData;
        }

        return $data;
    }
}
