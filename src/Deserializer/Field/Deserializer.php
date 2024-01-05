<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Field;

use SmartAssert\ServiceRequest\Exception\InvalidFieldDataException;
use SmartAssert\ServiceRequest\Field\Field;
use SmartAssert\ServiceRequest\Field\FieldInterface;
use SmartAssert\ServiceRequest\Field\Requirements;
use SmartAssert\ServiceRequest\Field\Size;

class Deserializer
{
    /**
     * @param array<mixed> $data
     *
     * @throws InvalidFieldDataException
     */
    public function deserialize(array $data): FieldInterface
    {
        $name = $data['name'] ?? null;
        $name = is_string($name) ? trim($name) : null;
        if (null === $name || '' === $name) {
            throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_NAME_MISSING);
        }

        $value = $data['value'] ?? null;
        if (null === $value) {
            throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_VALUE_MISSING);
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_scalar($item)) {
                    throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_VALUE_NOT_SCALAR);
                }
            }
        } else {
            if (!is_scalar($value)) {
                throw new InvalidFieldDataException($data, InvalidFieldDataException::CODE_VALUE_NOT_SCALAR);
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
