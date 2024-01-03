<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Field;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Exception\InvalidFieldDataException;
use SmartAssert\ServiceRequest\Field\Field;
use SmartAssert\ServiceRequest\Field\FieldInterface;
use SmartAssert\ServiceRequest\Field\Requirements;
use SmartAssert\ServiceRequest\Field\Size;

class FieldTest extends TestCase
{
    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testSerializeSuccess(FieldInterface $field, array $serialized): void
    {
        self::assertSame($serialized, $field->serialize());
    }

    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testDeserializeSuccess(FieldInterface $field, array $serialized): void
    {
        self::assertEquals(Field::deserialize($serialized), $field);
    }

    /**
     * @dataProvider serializeDataProvider
     */
    public function testSerializeDeserializeSuccess(FieldInterface $field): void
    {
        self::assertEquals($field, Field::deserialize($field->serialize()));
    }

    /**
     * @return array<mixed>
     */
    public static function serializeDataProvider(): array
    {
        $name = md5((string) rand());
        $randomInteger = rand();
        $randomString = md5((string) rand());

        return [
            'bool field, no requirements, ' => [
                'field' => new Field($name, true),
                'serialized' => [
                    'name' => $name,
                    'value' => true,
                ],
            ],
            'float field, no requirements, ' => [
                'field' => new Field($name, M_PI),
                'serialized' => [
                    'name' => $name,
                    'value' => M_PI,
                ],
            ],
            'int field, no requirements, ' => [
                'field' => new Field($name, $randomInteger),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomInteger,
                ],
            ],
            'string field, no requirements, ' => [
                'field' => new Field($name, $randomString),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                ],
            ],
            'custom field, has requirements, no size' => [
                'field' => (new Field($name, $randomString))->withRequirements(new Requirements('custom_type')),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                    'requirements' => [
                        'data_type' => 'custom_type',
                    ],
                ],
            ],
            'custom field, has requirements, has size (0), no maximum' => [
                'field' => (new Field($name, $randomString))->withRequirements(new Requirements(
                    'custom_type',
                    new Size(0, null)
                )),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                    'requirements' => [
                        'data_type' => 'custom_type',
                        'size' => [
                            'minimum' => 0,
                            'maximum' => null,
                        ],
                    ],
                ],
            ],
            'custom field, has requirements, has size (10), no maximum' => [
                'field' => (new Field($name, $randomString))->withRequirements(new Requirements(
                    'custom_type',
                    new Size(10, null)
                )),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                    'requirements' => [
                        'data_type' => 'custom_type',
                        'size' => [
                            'minimum' => 10,
                            'maximum' => null,
                        ],
                    ],
                ],
            ],
            'custom field, has requirements, has size' => [
                'field' => (new Field($name, $randomString))->withRequirements(new Requirements(
                    'custom_type',
                    new Size(1, 255)
                )),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                    'requirements' => [
                        'data_type' => 'custom_type',
                        'size' => [
                            'minimum' => 1,
                            'maximum' => 255,
                        ],
                    ],
                ],
            ],
            'string array field' => [
                'field' => (new Field($name, ['one', 'two', 'three']))->withErrorPosition(1),
                'serialized' => [
                    'name' => $name,
                    'value' => ['one', 'two', 'three'],
                    'position' => 1,
                ],
            ],
        ];
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
