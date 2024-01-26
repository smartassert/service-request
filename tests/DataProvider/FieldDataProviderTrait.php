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
    public static function parameterDataProvider(): array
    {
        $name = md5((string) rand());
        $randomInteger = rand();
        $randomString = md5((string) rand());

        return [
            'bool parameter, no requirements' => [
                'parameter' => new Parameter($name, true),
                'serialized' => [
                    'name' => $name,
                    'value' => true,
                ],
            ],
            'float parameter, no requirements' => [
                'parameter' => new Parameter($name, M_PI),
                'serialized' => [
                    'name' => $name,
                    'value' => M_PI,
                ],
            ],
            'int parameter, no requirements' => [
                'parameter' => new Parameter($name, $randomInteger),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomInteger,
                ],
            ],
            'string parameter, no requirements' => [
                'parameter' => new Parameter($name, $randomString),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                ],
            ],
            'custom parameter, has requirements, no size' => [
                'parameter' => (new Parameter($name, $randomString))->withRequirements(new Requirements('custom_type')),
                'serialized' => [
                    'name' => $name,
                    'value' => $randomString,
                    'requirements' => [
                        'data_type' => 'custom_type',
                    ],
                ],
            ],
            'custom parameter, has requirements, has size (0), no maximum' => [
                'parameter' => (new Parameter($name, $randomString))->withRequirements(new Requirements(
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
            'custom parameter, has requirements, has size (10), no maximum' => [
                'parameter' => (new Parameter($name, $randomString))->withRequirements(new Requirements(
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
            'custom parameter, has requirements, has size' => [
                'parameter' => (new Parameter($name, $randomString))->withRequirements(new Requirements(
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
            'string array parameter' => [
                'parameter' => (new Parameter($name, ['one', 'two', 'three']))->withErrorPosition(1),
                'serialized' => [
                    'name' => $name,
                    'value' => ['one', 'two', 'three'],
                    'position' => 1,
                ],
            ],
        ];
    }
}
