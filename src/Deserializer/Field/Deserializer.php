<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Field;

use SmartAssert\ServiceRequest\Exception\FieldValueEmptyException;
use SmartAssert\ServiceRequest\Exception\FieldValueInvalidException;
use SmartAssert\ServiceRequest\Exception\FieldValueMissingException;
use SmartAssert\ServiceRequest\Exception\FieldValueTypeErrorException;
use SmartAssert\ServiceRequest\Field\Field;
use SmartAssert\ServiceRequest\Field\FieldInterface;
use SmartAssert\ServiceRequest\Field\Requirements;
use SmartAssert\ServiceRequest\Field\RequirementsInterface;
use SmartAssert\ServiceRequest\Field\Size;
use SmartAssert\ServiceRequest\Field\SizeInterface;

class Deserializer
{
    /**
     * @param array<mixed> $data
     *
     * @throws FieldValueEmptyException
     * @throws FieldValueInvalidException
     * @throws FieldValueMissingException
     * @throws FieldValueTypeErrorException
     */
    public function deserialize(array $data): FieldInterface
    {
        $field = new Field($this->findName($data), $this->findValue($data));

        $errorPosition = $data['position'] ?? null;
        if (is_int($errorPosition)) {
            $field = $field->withErrorPosition($errorPosition);
        }

        $requirements = $this->createRequirements($data);
        if ($requirements instanceof RequirementsInterface) {
            $field = $field->withRequirements($requirements);
        }

        return $field;
    }

    /**
     * @param array<mixed> $data
     *
     * @return non-empty-string
     *
     * @throws FieldValueEmptyException
     * @throws FieldValueMissingException
     * @throws FieldValueTypeErrorException
     */
    private function findName(array $data): string
    {
        if (!array_key_exists('name', $data)) {
            throw new FieldValueMissingException('name', $data);
        }

        $name = $data['name'];
        if (!is_string($name)) {
            throw new FieldValueTypeErrorException('name', 'string', gettype($name), $data);
        }

        $name = trim($name);
        if ('' === $name) {
            throw new FieldValueEmptyException('name', $data);
        }

        return $name;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<scalar>|scalar
     *
     * @throws FieldValueEmptyException
     * @throws FieldValueMissingException
     * @throws FieldValueTypeErrorException
     */
    private function findValue(array $data): array|bool|float|int|string
    {
        if (!array_key_exists('value', $data)) {
            throw new FieldValueMissingException('value', $data);
        }

        $value = $data['value'];
        if (null === $value) {
            throw new FieldValueEmptyException('value', $data);
        }

        if (is_array($value)) {
            foreach ($value as $itemKey => $item) {
                if (!is_scalar($item)) {
                    throw new FieldValueTypeErrorException('value.' . $itemKey, 'scalar', gettype($item), $data);
                }
            }
        } else {
            if (!is_scalar($value)) {
                throw new FieldValueTypeErrorException('value', 'scalar', gettype($value), $data);
            }
        }

        return $value;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws FieldValueEmptyException
     * @throws FieldValueInvalidException
     * @throws FieldValueMissingException
     * @throws FieldValueTypeErrorException
     */
    private function createRequirements(array $data): ?RequirementsInterface
    {
        if (!array_key_exists('requirements', $data)) {
            return null;
        }

        $requirementsData = $data['requirements'];

        if (!array_key_exists('data_type', $requirementsData)) {
            throw new FieldValueMissingException('requirements.data_type', $data);
        }

        $dataType = $requirementsData['data_type'];
        if (!is_string($dataType)) {
            throw new FieldValueTypeErrorException('requirements.data_type', 'string', gettype($dataType), $data);
        }

        $dataType = trim($dataType);
        if ('' === $dataType) {
            throw new FieldValueEmptyException('requirements.data_type', $data);
        }

        return new Requirements($dataType, $this->createRequirementsSize($data));
    }

    /**
     * @param array<mixed> $data
     *
     * @throws FieldValueInvalidException
     */
    private function createRequirementsSize(array $data): ?SizeInterface
    {
        $requirementsData = $data['requirements'] ?? [];
        $requirementsData = is_array($requirementsData) ? $requirementsData : [];

        $sizeData = $requirementsData['size'] ?? null;

        if (is_array($sizeData)) {
            $minimum = $sizeData['minimum'] ?? null;
            $minimum = is_int($minimum) ? $minimum : null;
            if (null === $minimum) {
                throw new FieldValueInvalidException('requirements.size.minimum', $data);
            }

            $maximum = $sizeData['maximum'] ?? null;
            $maximum = is_int($maximum) ? $maximum : null;

            return new Size($minimum, $maximum);
        }

        return null;
    }
}
