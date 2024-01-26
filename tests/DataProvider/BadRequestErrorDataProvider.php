<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\DataProvider;

use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;

trait BadRequestErrorDataProvider
{
    use FieldDataProviderTrait;

    /**
     * @return array<mixed>
     */
    public static function badRequestErrorDataProvider(): array
    {
        $errorType = md5((string) rand());
        $dataSets = [];

        foreach (self::parameterDataProvider() as $fieldTestName => $data) {
            \assert(is_array($data));
            \assert(array_key_exists('parameter', $data));
            \assert(array_key_exists('serialized', $data));

            $testName = 'bad request error with field: ' . $fieldTestName;
            $dataSets[$testName] = [
                'error' => new BadRequestError($data['parameter'], $errorType),
                'serialized' => [
                    'class' => BadRequestErrorInterface::ERROR_CLASS,
                    'type' => $errorType,
                    'parameter' => $data['serialized'],
                ],
            ];
        }

        return $dataSets;
    }
}
