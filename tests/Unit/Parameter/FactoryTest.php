<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Parameter;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Parameter\Factory;

class FactoryTest extends TestCase
{
    /**
     * @dataProvider createStringParameterDataProvider
     *
     * @param non-empty-string $name
     */
    public function testCreateStringParameter(
        string $name,
        string $value,
        int $minimumLength,
        ?int $maximumLength
    ): void {
        $factory = new Factory();

        $parameter = $factory->createStringParameter($name, $value, $minimumLength, $maximumLength);

        self::assertSame($name, $parameter->getName());
        self::assertSame($value, $parameter->getValue());
        self::assertSame($minimumLength, $parameter->getRequirements()?->getSize()?->getMinimum());
        self::assertSame($maximumLength, $parameter->getRequirements()->getSize()->getMaximum());
    }

    /**
     * @return array<mixed>
     */
    public static function createStringParameterDataProvider(): array
    {
        return [
            'no maximum length' => [
                'name' => md5((string) rand()),
                'value' => md5((string) rand()),
                'minimumLength' => rand(0, 100),
                'maximumLength' => null,
            ],
            'has maximum length' => [
                'name' => md5((string) rand()),
                'value' => md5((string) rand()),
                'minimumLength' => rand(0, 100),
                'maximumLength' => rand(0, 100),
            ],
        ];
    }
}
