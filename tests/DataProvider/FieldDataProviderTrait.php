<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\DataProvider;

use SmartAssert\ServiceRequest\Parameter\Parameter;
use SmartAssert\ServiceRequest\Parameter\Requirements;
use SmartAssert\ServiceRequest\Parameter\Size;

trait FieldDataProviderTrait
{
    /**
     * @return array<mixed>
     */
    public static function fieldDataProvider(): array
    {
        $name = md5((string) rand());
        $randomInteger = rand();
        $randomString = md5((string) rand());

        return [
            'bool field, no requirements' => [
                'field' => new Parameter($name, true),
                'serialized' => [
                    'name' => $name,
                    'value' => true,
                ],
            ],
            'float field, no requirements' => [
                'field' => new Parameter($name, M_PI),
                'serialized' => [
                    'name' => $name,
                    'value' => M_PI,
                ],
            ],
            'int field, no requirements' => [
                'field' => new Parameter($name, $randomInteger),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomInteger,
                ],
            ],
            'string field, no requirements' => [
                'field' => new Parameter($name, $randomString),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                ],
            ],
            'custom field, has requirements, no size' => [
                'field' => (new Parameter($name, $randomString))->withRequirements(new Requirements('custom_type')),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                    'requirements' => [
                        'data_type' => 'custom_type',
                    ],
                ],
            ],
            'custom field, has requirements, has size (0), no maximum' => [
                'field' => (new Parameter($name, $randomString))->withRequirements(new Requirements(
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
                'field' => (new Parameter($name, $randomString))->withRequirements(new Requirements(
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
                'field' => (new Parameter($name, $randomString))->withRequirements(new Requirements(
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
                'field' => (new Parameter($name, ['one', 'two', 'three']))->withErrorPosition(1),
                'serialized' => [
                    'name' => $name,
                    'value' => ['one', 'two', 'three'],
                    'position' => 1,
                ],
            ],
        ];
    }
}
