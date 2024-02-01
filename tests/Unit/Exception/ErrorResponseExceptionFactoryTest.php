<?php

declare(strict_types=1);

namespace SmartAssert\ServiceRequest\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use SmartAssert\ServiceRequest\Error\BadRequestErrorInterface;
use SmartAssert\ServiceRequest\Error\DuplicateObjectErrorInterface;
use SmartAssert\ServiceRequest\Error\ErrorInterface;
use SmartAssert\ServiceRequest\Error\ModifyReadOnlyEntityErrorInterface;
use SmartAssert\ServiceRequest\Error\StorageErrorInterface;
use SmartAssert\ServiceRequest\Exception\ErrorResponseExceptionFactory;

class ErrorResponseExceptionFactoryTest extends TestCase
{
    private ErrorResponseExceptionFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ErrorResponseExceptionFactory();
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(ErrorInterface $error, ?\Throwable $previous, int $expectedExceptionCode): void
    {
        $exception = $this->factory->create($error, $previous);

        self::assertSame($error, $exception->error);
        self::assertSame($previous, $exception->getPrevious());
        self::assertSame($expectedExceptionCode, $exception->getCode());
    }

    /**
     * @return array<mixed>
     */
    public static function createDataProvider(): array
    {
        return [
            'generic, no previous exception' => [
                'error' => \Mockery::mock(ErrorInterface::class),
                'previous' => null,
                'expectedExceptionCode' => 400,
            ],
            'generic, with previous exception' => [
                'error' => \Mockery::mock(ErrorInterface::class),
                'previous' => new \Exception(),
                'expectedExceptionCode' => 400,
            ],
            'bad request, no previous exception' => [
                'error' => \Mockery::mock(BadRequestErrorInterface::class),
                'previous' => null,
                'expectedExceptionCode' => 400,
            ],
            'bad request, with previous exception' => [
                'error' => \Mockery::mock(BadRequestErrorInterface::class),
                'previous' => new \Exception(),
                'expectedExceptionCode' => 400,
            ],
            'duplicate object, no previous exception' => [
                'error' => \Mockery::mock(DuplicateObjectErrorInterface::class),
                'previous' => null,
                'expectedExceptionCode' => 400,
            ],
            'duplicate object, with previous exception' => [
                'error' => \Mockery::mock(DuplicateObjectErrorInterface::class),
                'previous' => new \Exception(),
                'expectedExceptionCode' => 400,
            ],
            'modify read-only entity, no previous exception' => [
                'error' => \Mockery::mock(ModifyReadOnlyEntityErrorInterface::class),
                'previous' => null,
                'expectedExceptionCode' => 405,
            ],
            'modify read-only entity, with previous exception' => [
                'error' => \Mockery::mock(ModifyReadOnlyEntityErrorInterface::class),
                'previous' => new \Exception(),
                'expectedExceptionCode' => 405,
            ],
            'storage, no previous exception' => [
                'error' => \Mockery::mock(StorageErrorInterface::class),
                'previous' => null,
                'expectedExceptionCode' => 500,
            ],
            'storage, with previous exception' => [
                'error' => \Mockery::mock(StorageErrorInterface::class),
                'previous' => new \Exception(),
                'expectedExceptionCode' => 500,
            ],
        ];
    }
}
