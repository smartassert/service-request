<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Field;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Exception\InvalidFieldDataException;
use SmartAssert\ServiceRequest\Field\Field;
use SmartAssert\ServiceRequest\Field\FieldInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class FieldTest extends TestCase
{
    use FieldDataProviderTrait;

    /**
     * @dataProvider fieldDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(FieldInterface $field, array $serialized): void
    {
        self::assertSame($serialized, $field->serialize());
    }

    /**
     * @dataProvider fieldDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testDeserializeSuccess(FieldInterface $field, array $serialized): void
    {
        self::assertEquals(Field::deserialize($serialized), $field);
    }

    /**
     * @dataProvider fieldDataProvider
     */
    public function testSerializeDeserializeSuccess(FieldInterface $field): void
    {
        self::assertEquals($field, Field::deserialize($field->serialize()));
    }

    /**
     * @dataProvider deserializeThrowsExceptionDataProvider
     *
     * @param array<mixed> $data
     */
    public function testDeserializeThrowsException(array $data, int $expectedExceptionCode): void
    {
        self::expectException(InvalidFieldDataException::class);
        self::expectExceptionCode($expectedExceptionCode);

        Field::deserialize($data);
    }

    /**
     * @return array<mixed>
     */
    public static function deserializeThrowsExceptionDataProvider(): array
    {
        return [
            'name missing' => [
                'data' => [],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_NAME_MISSING,
            ],
            'value missing' => [
                'data' => [
                    'name' => 'field_name',
                ],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_VALUE_MISSING,
            ],
            'value invalid type' => [
                'data' => [
                    'name' => 'field_name',
                    'value' => new \stdClass(),
                ],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_VALUE_NOT_SCALAR,
            ],
            'value in array invalid type' => [
                'data' => [
                    'name' => 'field_name',
                    'value' => [
                        new \stdClass(),
                    ],
                ],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_VALUE_NOT_SCALAR,
            ],
            'requirements data type missing' => [
                'data' => [
                    'name' => 'field_name',
                    'value' => '',
                    'requirements' => [],
                ],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_DATA_TYPE_EMPTY,
            ],
            'requirements size minimum missing' => [
                'data' => [
                    'name' => 'field_name',
                    'value' => '',
                    'requirements' => [
                        'data_type' => 'string',
                        'size' => [],
                    ],
                ],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_SIZE_MINIMUM_NOT_AN_INTEGER,
            ],
            'requirements size is not an integer' => [
                'data' => [
                    'name' => 'field_name',
                    'value' => '',
                    'requirements' => [
                        'data_type' => 'string',
                        'size' => [
                            'minimum' => true,
                        ],
                    ],
                ],
                'expectedExceptionCode' => InvalidFieldDataException::CODE_SIZE_MINIMUM_NOT_AN_INTEGER,
            ],
        ];
    }
}
