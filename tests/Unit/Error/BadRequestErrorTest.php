<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class BadRequestErrorTest extends TestCase
{
    use FieldDataProviderTrait;

    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(BadRequestErrorInterface $error, array $serialized): void
    {
        self::assertSame($serialized, $error->serialize());
    }

    /**
     * @return array<mixed>
     */
    public static function serializeDataProvider(): array
    {
        $errorType = md5((string) rand());
        $dataSets = [];

        foreach (self::fieldDataProvider() as $fieldTestName => $data) {
            \assert(is_array($data));
            \assert(array_key_exists('field', $data));
            \assert(array_key_exists('serialized', $data));

            $testName = 'with field: ' . $fieldTestName;
            $dataSets[$testName] = [
                'error' => new BadRequestError($data['field'], $errorType),
                'serialized' => [
                    'class' => BadRequestErrorInterface::ERROR_CLASS,
                    'type' => $errorType,
                    'field' => $data['serialized'],
                ],
            ];
        }

        return $dataSets;
    }
}
