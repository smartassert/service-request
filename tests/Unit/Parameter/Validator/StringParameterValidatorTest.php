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
use SmartAssert\ServiceRequest\Parameter\Validator\StringParameterValidator;

class StringParameterValidatorTest extends TestCase
{
    private StringParameterValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new StringParameterValidator(
            new ErrorResponseExceptionFactory(),
        );
    }

    /**
     * @dataProvider validStringInvalidDataProvider
     */
    public function testValidateStringInvalid(ParameterInterface $parameter, ErrorResponseException $expected): void
    {
        $exception = null;

        try {
            $this->validator->validateString($parameter);
        } catch (ErrorResponseException $exception) {
        }

        self::assertEquals($expected, $exception);
    }

    /**
     * @return array<mixed>
     */
    public static function validStringInvalidDataProvider(): array
    {
        $randomInteger = rand();
        $integerParameter = new Parameter(md5((string) rand()), $randomInteger);

        $minimumSize = rand(1, 10);
        $stringShorterThanMinimumSize = str_pad('', $minimumSize - 1, '.');

        $stringParameterWithMinimumSize = (new Parameter(
            md5((string) rand()),
            $stringShorterThanMinimumSize
        ))->withRequirements(
            new Requirements('string', new Size($minimumSize, null))
        );

        $maximumSize = rand(10, 20);
        $stringLongerThanMinimumSize = str_pad('', $maximumSize + 1, '.');

        $stringParameterWithMaximumSize = (new Parameter(
            md5((string) rand()),
            $stringLongerThanMinimumSize
        ))->withRequirements(
            new Requirements('string', new Size(0, $maximumSize))
        );

        return [
            'value is not a string' => [
                'parameter' => $integerParameter,
                'expected' => new ErrorResponseException(
                    new BadRequestError(
                        $integerParameter,
                        'wrong_type'
                    ),
                    400
                ),
            ],
            'value size is less than requirements' => [
                'parameter' => $stringParameterWithMinimumSize,
                'expected' => new ErrorResponseException(
                    new BadRequestError(
                        $stringParameterWithMinimumSize,
                        'wrong_size'
                    ),
                    400
                ),
            ],
            'value size is more than requirements' => [
                'parameter' => $stringParameterWithMaximumSize,
                'expected' => new ErrorResponseException(
                    new BadRequestError(
                        $stringParameterWithMaximumSize,
                        'wrong_size'
                    ),
                    400
                ),
            ],
        ];
    }

    public function testValidateNonEmptyStringInvalid(): void
    {
        $parameter = new Parameter(md5((string) rand()), '');

        $exception = null;

        try {
            $this->validator->validateNonEmptyString($parameter);
        } catch (ErrorResponseException $exception) {
        }

        self::assertEquals(
            new ErrorResponseException(
                new BadRequestError($parameter, 'wrong_size'),
                400
            ),
            $exception
        );
    }

    /**
     * @dataProvider validateStringValidDataProvider
     * @dataProvider validateNonEmptyStringValidDataProvider
     */
    public function testValidateStringValid(ParameterInterface $parameter): void
    {
        $output = $this->validator->validateString($parameter);

        self::assertSame($parameter->getValue(), $output);
    }

    /**
     * @return array<mixed>
     */
    public static function validateStringValidDataProvider(): array
    {
        return [
            'empty, no requirements' => [
                'parameter' => new Parameter(md5((string) rand()), ''),
            ],
        ];
    }

    /**
     * @dataProvider validateNonEmptyStringValidDataProvider
     */
    public function testValidateNonEmptyStringValid(ParameterInterface $parameter): void
    {
        $output = $this->validator->validateNonEmptyString($parameter);

        self::assertSame($parameter->getValue(), $output);
    }

    /**
     * @return array<mixed>
     */
    public static function validateNonEmptyStringValidDataProvider(): array
    {
        $minimumSize = rand(3, 10);
        $maximumSize = rand($minimumSize + 1, $minimumSize + 10);

        $longerThanMinimumSize = str_pad('', $minimumSize + 1, '.');
        $shorterThanMaximumSize = str_pad('', $maximumSize - 1, '.');

        return [
            'non-empty, no requirements' => [
                'parameter' => new Parameter(md5((string) rand()), md5((string) rand())),
            ],
            'no minimum size, has maximum size, less than maximum size' => [
                'parameter' => (new Parameter(
                    md5((string) rand()),
                    $shorterThanMaximumSize
                ))->withRequirements(
                    new Requirements('string', new Size(0, $maximumSize))
                ),
            ],
            'has minimum size, no maximum size, greater than minimum size' => [
                'parameter' => (new Parameter(
                    md5((string) rand()),
                    $longerThanMinimumSize
                ))->withRequirements(
                    new Requirements('string', new Size($minimumSize, null))
                ),
            ],
            'has minimum size, has maximum size, greater than minimum size and less than max' => [
                'parameter' => (new Parameter(
                    md5((string) rand()),
                    $longerThanMinimumSize
                ))->withRequirements(
                    new Requirements('string', new Size($minimumSize, $maximumSize))
                ),
            ],
        ];
    }
}
