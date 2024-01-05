<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Deserializer\Field;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Deserializer\Field\Deserializer;
use SmartAssert\ServiceRequest\Exception\FieldValueEmptyException;
use SmartAssert\ServiceRequest\Exception\FieldValueInvalidException;
use SmartAssert\ServiceRequest\Exception\FieldValueMissingException;
use SmartAssert\ServiceRequest\Exception\FieldValueTypeErrorException;
use SmartAssert\ServiceRequest\Field\FieldInterface;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class DeserializerTest extends TestCase
{
    use FieldDataProviderTrait;

    private Deserializer $deserializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deserializer = new Deserializer();
    }

    /**
     * @dataProvider fieldDataProvider
     *
     * @param array<mixed> $serialized
     */
    public function testDeserializeSuccess(FieldInterface $field, array $serialized): void
    {
        self::assertEquals($this->deserializer->deserialize($serialized), $field);
    }

    /**
     * @dataProvider deserializeThrowsExceptionDataProvider
     *
     * @param array<mixed> $data
     */
    public function testDeserializeThrowsException(array $data, \Throwable $expected): void
    {
        $exception = null;

        try {
            $this->deserializer->deserialize($data);
        } catch (\Throwable $exception) {
        }

        self::assertEquals($expected, $exception);
    }

    /**
     * @return array<mixed>
     */
    public static function deserializeThrowsExceptionDataProvider(): array
    {
        $name = md5((string) rand());

        return [
            'name missing' => [
                'data' => [],
                'expected' => new FieldValueMissingException('name', []),
            ],
            'name empty' => [
                'data' => ['name' => ''],
                'expected' => new FieldValueEmptyException('name', ['name' => '']),
            ],
            'name incorrect type' => [
                'data' => ['name' => true],
                'expected' => new FieldValueTypeErrorException(
                    'name',
                    'string',
                    'boolean',
                    ['name' => true],
                ),
            ],
            'value missing' => [
                'data' => ['name' => $name],
                'expected' => new FieldValueMissingException('value', ['name' => $name]),
            ],
            'value empty' => [
                'data' => ['name' => $name, 'value' => null],
                'expected' => new FieldValueEmptyException('value', ['name' => $name, 'value' => '']),
            ],
            'value not scalar' => [
                'data' => ['name' => $name, 'value' => new \stdClass()],
                'expected' => new FieldValueTypeErrorException(
                    'value',
                    'scalar',
                    'object',
                    ['name' => $name, 'value' => new \stdClass()],
                ),
            ],
            'value in array not scalar' => [
                'data' => ['name' => $name, 'value' => [new \stdClass()]],
                'expected' => new FieldValueTypeErrorException(
                    'value.0',
                    'scalar',
                    'object',
                    ['name' => $name, 'value' => [new \stdClass()]],
                ),
            ],
            'requirements data type missing' => [
                'data' => ['name' => $name, 'value' => '', 'requirements' => []],
                'expected' => new FieldValueMissingException(
                    'requirements.data_type',
                    ['name' => $name, 'value' => '', 'requirements' => []]
                ),
            ],
            'requirements data type empty' => [
                'data' => [
                    'name' => $name,
                    'value' => '',
                    'requirements' => [
                        'data_type' => '',
                    ],
                ],
                'expected' => new FieldValueEmptyException(
                    'requirements.data_type',
                    [
                        'name' => $name,
                        'value' => '',
                        'requirements' => [
                            'data_type' => '',
                        ],
                    ],
                ),
            ],
            'requirements data type incorrect type' => [
                'data' => [
                    'name' => $name,
                    'value' => '',
                    'requirements' => [
                        'data_type' => 123,
                    ],
                ],
                'expected' => new FieldValueTypeErrorException(
                    'requirements.data_type',
                    'string',
                    'integer',
                    [
                        'name' => $name,
                        'value' => '',
                        'requirements' => [
                            'data_type' => 123,
                        ],
                    ],
                ),
            ],
            'requirements size minimum not an integer' => [
                'data' => [
                    'name' => $name,
                    'value' => '',
                    'requirements' => [
                        'data_type' => 'string',
                        'size' => [
                            'minimum' => 'string value',
                        ],
                    ],
                ],
                'expected' => new FieldValueInvalidException(
                    'requirements.size.minimum',
                    [
                        'name' => $name,
                        'value' => '',
                        'requirements' => [
                            'data_type' => 'string',
                            'size' => [
                                'minimum' => 'string value',
                            ],
                        ],
                    ],
                ),
            ],
        ];
    }
}
