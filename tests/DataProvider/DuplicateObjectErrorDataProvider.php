<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\DataProvider;

use SmartAssert\ServiceRequest\Error\DuplicateObjectError;
use SmartAssert\ServiceRequest\Error\DuplicateObjectErrorInterface;

trait DuplicateObjectErrorDataProvider
{
    use FieldDataProviderTrait;

    /**
     * @return array<mixed>
     */
    public static function duplicateObjectErrorDataProvider(): array
    {
        $dataSets = [];

        foreach (self::fieldDataProvider() as $fieldTestName => $data) {
            \assert(is_array($data));
            \assert(array_key_exists('field', $data));
            \assert(array_key_exists('serialized', $data));

            $testName = 'duplicate object error with field: ' . $fieldTestName;
            $dataSets[$testName] = [
                'error' => new DuplicateObjectError($data['field']),
                'serialized' => [
                    'class' => DuplicateObjectErrorInterface::ERROR_CLASS,
                    'field' => $data['serialized'],
                ],
            ];
        }

        return $dataSets;
    }
}
