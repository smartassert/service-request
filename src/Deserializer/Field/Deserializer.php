<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Deserializer\Field;

use SmartAssert\ServiceRequest\Exception\DeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;
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
     * @throws DeserializationException
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
     * @throws DeserializationException
     */
    private function findName(array $data): string
    {
        if (!array_key_exists('name', $data)) {
            throw new DeserializationException('name', $data, DeserializationException::CODE_MISSING);
        }

        $name = $data['name'];
        if (!is_string($name)) {
            throw (new DeserializationException(
                'name',
                $data,
                DeserializationException::CODE_INVALID
            ))->withContext(new TypeErrorContext('string', gettype($name)));
        }

        $name = trim($name);
        if ('' === $name) {
            throw new DeserializationException('name', $data, DeserializationException::CODE_EMPTY);
        }

        return $name;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<scalar>|scalar
     *
     * @throws DeserializationException
     */
    private function findValue(array $data): array|bool|float|int|string
    {
        if (!array_key_exists('value', $data)) {
            throw new DeserializationException('value', $data, DeserializationException::CODE_MISSING);
        }

        $value = $data['value'];
        if (null === $value) {
            throw new DeserializationException('value', $data, DeserializationException::CODE_EMPTY);
        }

        if (is_array($value)) {
            foreach ($value as $itemKey => $item) {
                if (!is_scalar($item)) {
                    throw (new DeserializationException(
                        'value.' . $itemKey,
                        $data,
                        DeserializationException::CODE_INVALID
                    ))->withContext(new TypeErrorContext('scalar', gettype($item)));
                }
            }
        } else {
            if (!is_scalar($value)) {
                throw (new DeserializationException(
                    'value',
                    $data,
                    DeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('scalar', gettype($value)));
            }
        }

        return $value;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws DeserializationException
     */
    private function createRequirements(array $data): ?RequirementsInterface
    {
        if (!array_key_exists('requirements', $data)) {
            return null;
        }

        $requirementsData = $data['requirements'];

        if (!array_key_exists('data_type', $requirementsData)) {
            throw new DeserializationException(
                'requirements.data_type',
                $data,
                DeserializationException::CODE_MISSING
            );
        }

        $dataType = $requirementsData['data_type'];
        if (!is_string($dataType)) {
            throw (new DeserializationException(
                'requirements.data_type',
                $data,
                DeserializationException::CODE_INVALID
            ))->withContext(new TypeErrorContext('string', gettype($dataType)));
        }

        $dataType = trim($dataType);
        if ('' === $dataType) {
            throw new DeserializationException(
                'requirements.data_type',
                $data,
                DeserializationException::CODE_EMPTY
            );
        }

        return new Requirements($dataType, $this->createRequirementsSize($data));
    }

    /**
     * @param array<mixed> $data
     *
     * @throws DeserializationException
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
                throw new DeserializationException(
                    'requirements.size.minimum',
                    $data,
                    DeserializationException::CODE_INVALID
                );
            }

            $maximum = $sizeData['maximum'] ?? null;
            $maximum = is_int($maximum) ? $maximum : null;

            return new Size($minimum, $maximum);
        }

        return null;
    }
}
