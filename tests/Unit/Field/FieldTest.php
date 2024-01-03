<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Field;

use PHPUnit\Framework\TestCase;
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
    public function testSerialize(FieldInterface $field, array $serialized): void
    {
        self::assertSame($serialized, $field->serialize());
    }

    /**
     * @dataProvider serializeDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testDeserialize(FieldInterface $field, array $serialized): void
    {
        self::assertEquals(Field::deserialize($serialized), $field);
    }

    /**
     * @dataProvider serializeDataProvider
     */
    public function testSerializeDeserialize(FieldInterface $field): void
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
}
