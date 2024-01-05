<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\DuplicateObjectError;
use SmartAssert\ServiceRequest\Error\DuplicateObjectErrorInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class DuplicateObjectErrorTest extends TestCase
{
    use FieldDataProviderTrait;

    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(DuplicateObjectErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }

    /**
     * @return array<mixed>
     */
    public static function serializeDataProvider(): array
    {
        $dataSets = [];

        foreach (self::fieldDataProvider() as $fieldTestName => $data) {
            \assert(is_array($data));
            \assert(array_key_exists('field', $data));
            \assert(array_key_exists('serialized', $data));

            $testName = 'with field: ' . $fieldTestName;
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
