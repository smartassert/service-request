<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Field;

use SmartAssert\ServiceRequest\Exception\InvalidFieldDataException;

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

    /**
     * @param array<mixed> $data
     *
     * @throws InvalidFieldDataException
     */
    public static function deserialize(array $data): FieldInterface
    {
        $name = $data['name'] ?? null;
        $name = is_string($name) ? trim($name) : null;
        if (null === $name || '' === $name) {
            throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_NAME_MISSING);
        }

        $value = $data['value'] ?? null;
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_bool($item) && !is_float($item) && !is_int($item) && !is_string($item)) {
                    throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_VALUE_NOT_SCALAR);
                }
            }
        }

        $field = new Field($name, $value);

        $errorPosition = $data['position'] ?? null;
        if (is_int($errorPosition)) {
            $field = $field->withErrorPosition($errorPosition);
        }

        $requirementsData = $data['requirements'] ?? null;
        if (is_array($requirementsData)) {
            $dataType = $requirementsData['data_type'] ?? null;
            $dataType = is_string($dataType) ? trim($dataType) : null;
            if (null === $dataType || '' === $dataType) {
                throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_DATA_TYPE_EMPTY);
            }

            $size = null;
            $sizeData = $requirementsData['size'] ?? null;
            if (is_array($sizeData)) {
                $minimum = $sizeData['minimum'] ?? null;
                $minimum = is_int($minimum) ? $minimum : null;
                if (null === $minimum) {
                    throw new InvalidFieldDataException(
                        $data,
                        InvalidFieldDataException::CODE_SIZE_MINIMUM_NOT_AN_INTEGER
                    );
                }

                $maximum = $sizeData['maximum'] ?? null;
                $maximum = is_int($maximum) ? $maximum : null;

                $size = new Size($minimum, $maximum);
            }

            $field = $field->withRequirements(new Requirements($dataType, $size));
        }

        return $field;
    }
}
