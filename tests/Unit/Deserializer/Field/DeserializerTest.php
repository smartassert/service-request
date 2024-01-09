<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Deserializer\Field;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Deserializer\Field\Deserializer;
use SmartAssert\ServiceRequest\Exception\FieldDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;
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
                'expected' => new FieldDeserializationException(
                    'name',
                    [],
                    FieldDeserializationException::CODE_MISSING
                ),
            ],
            'name empty' => [
                'data' => ['name' => ''],
                'expected' => new FieldDeserializationException(
                    'name',
                    ['name' => ''],
                    FieldDeserializationException::CODE_EMPTY
                ),
            ],
            'name incorrect type' => [
                'data' => ['name' => true],
                'expected' => (new FieldDeserializationException(
                    'name',
                    ['name' => true],
                    FieldDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'boolean')),
            ],
            'value missing' => [
                'data' => ['name' => $name],
                'expected' => new FieldDeserializationException(
                    'value',
                    ['name' => $name],
                    FieldDeserializationException::CODE_MISSING
                ),
            ],
            'value empty' => [
                'data' => ['name' => $name, 'value' => null],
                'expected' => new FieldDeserializationException(
                    'value',
                    ['name' => $name, 'value' => ''],
                    FieldDeserializationException::CODE_EMPTY
                ),
            ],
            'value not scalar' => [
                'data' => ['name' => $name, 'value' => new \stdClass()],
                'expected' => (new FieldDeserializationException(
                    'value',
                    ['name' => $name, 'value' => new \stdClass()],
                    FieldDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('scalar', 'object')),
            ],
            'value in array not scalar' => [
                'data' => ['name' => $name, 'value' => [new \stdClass()]],
                'expected' => (new FieldDeserializationException(
                    'value.0',
                    ['name' => $name, 'value' => [new \stdClass()]],
                    FieldDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('scalar', 'object')),
            ],
            'requirements data type missing' => [
                'data' => ['name' => $name, 'value' => '', 'requirements' => []],
                'expected' => new FieldDeserializationException(
                    'requirements.data_type',
                    ['name' => $name, 'value' => '', 'requirements' => []],
                    FieldDeserializationException::CODE_MISSING
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
                'expected' => new FieldDeserializationException(
                    'requirements.data_type',
                    [
                        'name' => $name,
                        'value' => '',
                        'requirements' => [
                            'data_type' => '',
                        ],
                    ],
                    FieldDeserializationException::CODE_EMPTY
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
                'expected' => (new FieldDeserializationException(
                    'requirements.data_type',
                    [
                        'name' => $name,
                        'value' => '',
                        'requirements' => [
                            'data_type' => 123,
                        ],
                    ],
                    FieldDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
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
                'expected' => new FieldDeserializationException(
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
                    FieldDeserializationException::CODE_INVALID
                ),
            ],
        ];
    }
}
