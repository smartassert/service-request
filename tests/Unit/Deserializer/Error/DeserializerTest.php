<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Deserializer\Error;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Deserializer\Error\BadRequestErrorDeserializer;
use SmartAssert\ServiceRequest\Deserializer\Error\Deserializer;
use SmartAssert\ServiceRequest\Deserializer\Field\Deserializer as FieldDeserializer;
use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorValueEmptyException;
use SmartAssert\ServiceRequest\Exception\ErrorValueInvalidException;
use SmartAssert\ServiceRequest\Exception\ErrorValueMissingException;
use SmartAssert\ServiceRequest\Exception\ErrorValueTypeErrorException;
use SmartAssert\ServiceRequest\Exception\FieldValueMissingException;
use SmartAssert\ServiceRequest\Exception\UnknownErrorClassException;
use SmartAssert\ServiceRequest\Tests\DataProvider\FieldDataProviderTrait;

class DeserializerTest extends TestCase
{
    use FieldDataProviderTrait;

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
                'expected' => new ErrorValueMissingException(null, 'class', []),
            ],
            'error class empty' => [
                'deserializer' => new Deserializer([]),
                'data' => [
                    'class' => '',
                ],
                'expected' => new ErrorValueEmptyException(
                    null,
                    'class',
                    [
                        'class' => '',
                    ]
                ),
            ],
            'error class not a string' => [
                'deserializer' => new Deserializer([]),
                'data' => [
                    'class' => 123,
                ],
                'expected' => new ErrorValueTypeErrorException(
                    null,
                    'class',
                    'string',
                    'integer',
                    [
                        'class' => 123,
                    ]
                ),
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
                'expected' => new ErrorValueMissingException(
                    'bad_request',
                    'type',
                    [
                        'class' => 'bad_request',
                    ],
                ),
            ],
            'bad request error type empty' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => '',
                ],
                'expected' => new ErrorValueEmptyException(
                    'bad_request',
                    'type',
                    [
                        'class' => 'bad_request',
                        'type' => '',
                    ],
                ),
            ],
            'bad request error type not a string' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 123,
                ],
                'expected' => new ErrorValueTypeErrorException(
                    'bad_request',
                    'type',
                    'string',
                    'integer',
                    [
                        'class' => 'bad_request',
                        'type' => 123,
                    ],
                ),
            ],
            'bad request error field missing' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 'too_large',
                ],
                'expected' => new ErrorValueMissingException(
                    'bad_request',
                    'field',
                    [
                        'class' => 'bad_request',
                        'type' => 'too_large',
                    ],
                ),
            ],
            'bad request error field not an array' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 'too_large',
                    'field' => 123,
                ],
                'expected' => new ErrorValueTypeErrorException(
                    'bad_request',
                    'field',
                    'array',
                    'integer',
                    [
                        'class' => 'bad_request',
                        'type' => 'too_large',
                        'field' => 123,
                    ],
                ),
            ],
            'bad request error field invalid' => [
                'deserializer' => self::createDeserializer(),
                'data' => [
                    'class' => 'bad_request',
                    'type' => 'too_large',
                    'field' => [],
                ],
                'expected' => new ErrorValueInvalidException(
                    'bad_request',
                    'field',
                    [
                        'class' => 'bad_request',
                        'type' => 'too_large',
                        'field' => [],
                    ],
                    new FieldValueMissingException('name', []),
                ),
            ],
        ];
    }

    /**
     * @dataProvider deserializeDataProvider
     *
     * @param array<mixed> $data
     */
    public function testDeserializeSuccess(ErrorInterface $expected, array $data): void
    {
        self::assertEquals($expected, $this->createDeserializer()->deserialize($data));
    }

    /**
     * @return array<mixed>
     */
    public static function deserializeDataProvider(): array
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

    public static function createDeserializer(): Deserializer
    {
        $typeDeserializers = [
            new BadRequestErrorDeserializer(
                new FieldDeserializer()
            ),
        ];

        return new Deserializer($typeDeserializers);
    }
}
