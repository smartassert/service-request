<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Parameter\Validator;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\BadRequestError;
use SmartAssert\ServiceRequest\Exception\ErrorResponseException;
use SmartAssert\ServiceRequest\Exception\ErrorResponseExceptionFactory;
use SmartAssert\ServiceRequest\Parameter\Parameter;
use SmartAssert\ServiceRequest\Parameter\ParameterInterface;
use SmartAssert\ServiceRequest\Parameter\Requirements;
use SmartAssert\ServiceRequest\Parameter\Size;
use SmartAssert\ServiceRequest\Parameter\Validator\PositiveIntegerParameterValidator;

class PositiveIntegerParameterValidatorTest extends TestCase
{
    private PositiveIntegerParameterValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new PositiveIntegerParameterValidator(
            new ErrorResponseExceptionFactory(),
        );
    }

    /**
     * @dataProvider validateIntegerInvalidDataProvider
     */
    public function testValidateIntegerInvalid(ParameterInterface $parameter, ErrorResponseException $expected): void
    {
        $exception = null;

        try {
            $this->validator->validateInteger($parameter);
        } catch (ErrorResponseException $exception) {
        }

        self::assertEquals($expected, $exception);
    }

    /**
     * @return array<mixed>
     */
    public static function validateIntegerInvalidDataProvider(): array
    {
        $randomString = md5((string) rand());
        $stringParameter = new Parameter(md5((string) rand()), $randomString);

        $negativeInteger = rand(-1, -100);
        $negativeIntegerParameter = new Parameter(md5((string) rand()), $negativeInteger);

        $zero = 0;
        $zeroParameter = new Parameter(md5((string) rand()), $zero);

        $minimumSize = rand(2, 100);
        $integerSmallerThanMinimumSize = rand(1, $minimumSize);

        $integerParameterWithMinimumSize = (new Parameter(
            md5((string) rand()),
            $integerSmallerThanMinimumSize
        ))->withRequirements(
            new Requirements('integer', new Size($minimumSize, null))
        );

        $maximumSize = rand(10, 20);
        $integerGreaterThanMaximumSize = rand($maximumSize + 1, $maximumSize + 100);

        $integerParameterWithMaximumSize = (new Parameter(
            md5((string) rand()),
            $integerGreaterThanMaximumSize
        ))->withRequirements(
            new Requirements('integer', new Size(0, $maximumSize))
        );

        return [
            'value is not an integer' => [
                'parameter' => $stringParameter,
                'expected' => new ErrorResponseException(
                    new BadRequestError($stringParameter, 'wrong_type'),
                    400
                ),
            ],
            'value is negative' => [
                'parameter' => $negativeIntegerParameter,
                'expected' => new ErrorResponseException(
                    new BadRequestError($negativeIntegerParameter, 'wrong_size'),
                    400
                ),
            ],
            'value is zero' => [
                'parameter' => $zeroParameter,
                'expected' => new ErrorResponseException(
                    new BadRequestError($zeroParameter, 'wrong_size'),
                    400
                ),
            ],
            'value size is less than requirements' => [
                'parameter' => $integerParameterWithMinimumSize,
                'expected' => new ErrorResponseException(
                    new BadRequestError($integerParameterWithMinimumSize, 'wrong_size'),
                    400
                ),
            ],
            'value size is greater than requirements' => [
                'parameter' => $integerParameterWithMaximumSize,
                'expected' => new ErrorResponseException(
                    new BadRequestError($integerParameterWithMaximumSize, 'wrong_size'),
                    400
                ),
            ],
        ];
    }
}
