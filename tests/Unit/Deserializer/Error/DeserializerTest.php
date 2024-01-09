<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Deserializer\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Deserializer\Error\BadRequestErrorDeserializer;
use SmartAssert\ServiceRequest\Deserializer\Error\Deserializer;
use SmartAssert\ServiceRequest\Deserializer\Error\DuplicateObjectErrorDeserializer;
use SmartAssert\ServiceRequest\Deserializer\Error\ErrorFieldDeserializer;
use SmartAssert\ServiceRequest\Deserializer\Error\ModifyReadOnlyEntityDeserializer;
use SmartAssert\ServiceRequest\Deserializer\Error\StorageErrorDeserializer;
use SmartAssert\ServiceRequest\Deserializer\Field\Deserializer as FieldDeserializer;
use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorDeserializationException;
use SmartAssert\ServiceRequest\Exception\FieldDeserializationException;
use SmartAssert\ServiceRequest\Exception\TypeErrorContext;
use SmartAssert\ServiceRequest\Exception\UnknownErrorClassException;
use SmartAssert\ServiceRequest\Tests\DataProvider\BadRequestErrorDataProvider;
use SmartAssert\ServiceRequest\Tests\DataProvider\DuplicateObjectErrorDataProvider;
use SmartAssert\ServiceRequest\Tests\DataProvider\ModifyReadOnlyEntityErrorTrait;
use SmartAssert\ServiceRequest\Tests\DataProvider\StorageErrorDataProviderTrait;

class DeserializerTest extends TestCase
{
    use BadRequestErrorDataProvider;
    use DuplicateObjectErrorDataProvider;
    use ModifyReadOnlyEntityErrorTrait;
    use StorageErrorDataProviderTrait;

    /**
     * @dataProvider deserializeThrowsExceptionDataProvider
     *
     * @param array<mixed> $data
     */
    public function testDeserializeThrowsException(Deserializer $deserializer, array $data, \Throwable $expected): void
    {
        $exception = null;

        try {
            $deserializer->deserialize($data);
        } catch (\Throwable $exception) {
        }

        self::assertEquals($expected, $exception);
    }

    /**
     * @return array<mixed>
     */
    public static function deserializeThrowsExceptionDataProvider(): array
    {
        return [
            'error class missing' => [
                'deserializer' => new Deserializer([]),
                'data' => [],
                'expected' => new ErrorDeserializationException(
                    '',
                    'class',
                    [],
                    ErrorDeserializationException::CODE_MISSING
                ),
            ],
            'error class empty' => [
                'deserializer' => new Deserializer([]),
                'data' => [
                    'class' => '',
                ],
                'expected' => new ErrorDeserializationException(
                    '',
                    'class',
                    [
                        'class' => '',
                    ],
                    ErrorDeserializationException::CODE_EMPTY,
                ),
            ],
            'error class not a string' => [
                'deserializer' => new Deserializer([]),
                'data' => [
                    'class' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    '',
                    'class',
                    [
                        'class' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'unknown error class' => [
                'deserializer' => new Deserializer([]),
                'data' => [
                    'class' => 'unknown',
                ],
                'expected' => new UnknownErrorClassException(
                    'unknown',
                    [
                        'class' => 'unknown',
                    ]
                ),
            ],
            'bad request error type missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                ],
                'expected' => new ErrorDeserializationException(
                    'bad_request',
                    'type',
                    [
                        'class' => 'bad_request',
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'bad request error type empty' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => '',
                ],
                'expected' => new ErrorDeserializationException(
                    'bad_request',
                    'type',
                    [
                        'class' => 'bad_request',
                        'type' => '',
                    ],
                    ErrorDeserializationException::CODE_EMPTY
                ),
            ],
            'bad request error type not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'bad_request',
                    'type',
                    [
                        'class' => 'bad_request',
                        'type' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'bad request error field missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 'too_large',
                ],
                'expected' => new ErrorDeserializationException(
                    'bad_request',
                    'field',
                    [
                        'class' => 'bad_request',
                        'type' => 'too_large',
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'bad request error field not an array' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 'too_large',
                    'field' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'bad_request',
                    'field',
                    [
                        'class' => 'bad_request',
                        'type' => 'too_large',
                        'field' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('array', 'integer')),
            ],
            'bad request error field invalid' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 'too_large',
                    'field' => [],
                ],
                'expected' => new ErrorDeserializationException(
                    'bad_request',
                    'field',
                    [
                        'class' => 'bad_request',
                        'type' => 'too_large',
                        'field' => [],
                    ],
                    ErrorDeserializationException::CODE_INVALID,
                    new FieldDeserializationException('name', [], FieldDeserializationException::CODE_MISSING)
                ),
            ],
            'duplicate object error field missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'duplicate',
                ],
                'expected' => new ErrorDeserializationException(
                    'duplicate',
                    'field',
                    [
                        'class' => 'duplicate',
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'duplicate error field not an array' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'duplicate',
                    'field' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'duplicate',
                    'field',
                    [
                        'class' => 'duplicate',
                        'field' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('array', 'integer')),
            ],
            'duplicate object error field invalid' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'duplicate',
                    'field' => [],
                ],
                'expected' => new ErrorDeserializationException(
                    'duplicate',
                    'field',
                    [
                        'class' => 'duplicate',
                        'field' => [],
                    ],
                    ErrorDeserializationException::CODE_INVALID,
                    new FieldDeserializationException('name', [], FieldDeserializationException::CODE_MISSING)
                ),
            ],
            'modify read-only entity error entity missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                ],
                'expected' => new ErrorDeserializationException(
                    'modify_read_only',
                    'entity',
                    [
                        'class' => 'modify_read_only',
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'modify read-only entity entity not an array' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'modify_read_only',
                    'entity',
                    [
                        'class' => 'modify_read_only',
                        'entity' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('array', 'integer')),
            ],
            'modify read-only entity entity.id missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => [],
                ],
                'expected' => new ErrorDeserializationException(
                    'modify_read_only',
                    'entity.id',
                    [
                        'class' => 'modify_read_only',
                        'entity' => [],
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'modify read-only entity entity.id empty' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => [
                        'id' => '',
                    ],
                ],
                'expected' => new ErrorDeserializationException(
                    'modify_read_only',
                    'entity.id',
                    [
                        'class' => 'modify_read_only',
                        'entity' => [
                            'id' => '',
                        ],
                    ],
                    ErrorDeserializationException::CODE_EMPTY,
                ),
            ],
            'modify read-only entity entity.id not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => [
                        'id' => 123,
                    ],
                ],
                'expected' => (new ErrorDeserializationException(
                    'modify_read_only',
                    'entity.id',
                    [
                        'class' => 'modify_read_only',
                        'entity' => [
                            'id' => 123,
                        ],
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'modify read-only entity entity.type missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => [
                        'id' => 'entity_id',
                    ],
                ],
                'expected' => new ErrorDeserializationException(
                    'modify_read_only',
                    'entity.type',
                    [
                        'class' => 'modify_read_only',
                        'entity' => [
                            'id' => 'entity_id',
                        ],
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'modify read-only entity entity.type empty' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => [
                        'id' => 'entity_id',
                        'type' => '',
                    ],
                ],
                'expected' => new ErrorDeserializationException(
                    'modify_read_only',
                    'entity.type',
                    [
                        'class' => 'modify_read_only',
                        'entity' => [
                            'id' => 'entity_id',
                            'type' => '',
                        ],
                    ],
                    ErrorDeserializationException::CODE_EMPTY,
                ),
            ],
            'modify read-only entity entity.type not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'modify_read_only',
                    'entity' => [
                        'id' => 'entity_id',
                        'type' => 123,
                    ],
                ],
                'expected' => (new ErrorDeserializationException(
                    'modify_read_only',
                    'entity.type',
                    [
                        'class' => 'modify_read_only',
                        'entity' => [
                            'id' => 'entity_id',
                            'type' => 123,
                        ],
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'storage error type is not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'storage',
                    'type' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'storage',
                    'type',
                    [
                        'class' => 'storage',
                        'type' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'storage error object type missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'storage',
                    'type' => null,
                ],
                'expected' => new ErrorDeserializationException(
                    'storage',
                    'object_type',
                    [
                        'class' => 'storage',
                        'type' => null,
                    ],
                    ErrorDeserializationException::CODE_MISSING,
                ),
            ],
            'storage error object type empty' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'storage',
                    'type' => null,
                    'object_type' => '',
                ],
                'expected' => new ErrorDeserializationException(
                    'storage',
                    'object_type',
                    [
                        'class' => 'storage',
                        'type' => null,
                        'object_type' => '',
                    ],
                    ErrorDeserializationException::CODE_EMPTY,
                ),
            ],
            'storage error object type not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'storage',
                    'type' => null,
                    'object_type' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'storage',
                    'object_type',
                    [
                        'class' => 'storage',
                        'type' => null,
                        'object_type' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'storage error location is not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'storage',
                    'type' => null,
                    'object_type' => 'file_source',
                    'location' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'storage',
                    'location',
                    [
                        'class' => 'storage',
                        'type' => null,
                        'object_type' => 'file_source',
                        'location' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('string', 'integer')),
            ],
            'storage error context is not an array' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'storage',
                    'type' => null,
                    'object_type' => 'file_source',
                    'location' => null,
                    'context' => 123,
                ],
                'expected' => (new ErrorDeserializationException(
                    'storage',
                    'context',
                    [
                        'class' => 'storage',
                        'type' => null,
                        'object_type' => 'file_source',
                        'location' => null,
                        'context' => 123,
                    ],
                    ErrorDeserializationException::CODE_INVALID
                ))->withContext(new TypeErrorContext('array', 'integer')),
            ],
        ];
    }

    /**
     * @dataProvider badRequestErrorDataProvider
     * @dataProvider duplicateObjectErrorDataProvider
     * @dataProvider modifyReadOnlyEntityDataProvider
     * @dataProvider storageErrorDataProvider
     *
     * @param array<mixed> $data
     */
    public function testDeserializeSuccess(ErrorInterface $expected, array $data): void
    {
        self::assertEquals($expected, $this->createDeserializer()->deserialize($data));
    }

    public static function createDeserializer(): Deserializer
    {
        $errorFieldDeserializer = new ErrorFieldDeserializer(new FieldDeserializer());

        return new Deserializer([
            new BadRequestErrorDeserializer($errorFieldDeserializer),
            new DuplicateObjectErrorDeserializer($errorFieldDeserializer),
            new ModifyReadOnlyEntityDeserializer(),
            new StorageErrorDeserializer(),
        ]);
    }
}
