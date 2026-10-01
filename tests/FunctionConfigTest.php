<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction\Tests;

use ChristianBrown\CloudRunFunction\FunctionConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FunctionConfig::class)]
final class FunctionConfigTest extends TestCase
{
    public function test(): void
    {
        $functionConfig = new FunctionConfig('test-krevision');
        self::assertSame('test-krevision', $functionConfig->getKrevision());

        self::assertFalse($functionConfig->getAllowLocalOrigins());
        self::assertFalse($functionConfig->getAllowUnauthenticated());
        self::assertFalse($functionConfig->getDebug());
        self::assertNull($functionConfig->getRequiredHeaderKey());
        self::assertNull($functionConfig->getRequiredHeaderValue());
        self::assertNull($functionConfig->getRequiredOrigin());
        self::assertNull($functionConfig->getSurrogateKey());
        self::assertNull($functionConfig->getUseCacheTtl());
        self::assertNull($functionConfig->getUseBrowserCacheTtl());
        self::assertNull($functionConfig->getUseCacheButRequestTtl());
        self::assertNull($functionConfig->getUseCacheIfErrorTtl());

        $changed = $functionConfig
            ->withAllowLocalOrigins(true)
            ->withAllowUnauthenticated(true)
            ->withDebug(true)
            ->withRequiredHeaderKey('test-required-header-key')
            ->withRequiredHeaderValue('test-required-header-value')
            ->withRequiredOrigin('test-required-origin')
            ->withSurrogateKey('test-surrogate-key')
            ->withUseCacheTtl(3600)
            ->withUseBrowserCacheTtl(0)
            ->withUseCacheButRequestTtl(7200)
            ->withUseCacheIfErrorTtl(259200);

        // The original is untouched: a with-er returns a new instance.
        self::assertFalse($functionConfig->getDebug());
        self::assertNull($functionConfig->getUseCacheTtl());

        self::assertSame('test-krevision', $changed->getKrevision());
        self::assertTrue($changed->getAllowLocalOrigins());
        self::assertTrue($changed->getAllowUnauthenticated());
        self::assertTrue($changed->getDebug());
        self::assertSame('test-required-header-key', $changed->getRequiredHeaderKey());
        self::assertSame('test-required-header-value', $changed->getRequiredHeaderValue());
        self::assertSame('test-required-origin', $changed->getRequiredOrigin());
        self::assertSame('test-surrogate-key', $changed->getSurrogateKey());
        self::assertSame(3600, $changed->getUseCacheTtl());
        self::assertSame(0, $changed->getUseBrowserCacheTtl());
        self::assertSame(7200, $changed->getUseCacheButRequestTtl());
        self::assertSame(259200, $changed->getUseCacheIfErrorTtl());
    }
}
