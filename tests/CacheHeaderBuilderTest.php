<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction\Tests;

use ChristianBrown\CloudRunFunction\CacheHeaderBuilder;
use ChristianBrown\CloudRunFunction\FunctionConfigInterface;
use ChristianBrown\CloudRunFunction\ResponseInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

#[CoversClass(CacheHeaderBuilder::class)]
final class CacheHeaderBuilderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAddsCacheAndSurrogateControlWhenTtlsSet(): void
    {
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getUseCacheTtl')
            ->willReturn(3600);
        $functionConfig->method('getUseCacheButRequestTtl')
            ->willReturn(7200);
        $functionConfig->method('getUseCacheIfErrorTtl')
            ->willReturn(259200);

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        self::assertSame('s-maxage=3600, max-age=3600, stale-while-revalidate=7200, stale-if-error=259200', $headers[ResponseInterface::HEADER_KEY_CACHE_CONTROL]);
        self::assertSame('max-age=3600, stale-while-revalidate=7200, stale-if-error=259200', $headers[ResponseInterface::HEADER_KEY_SURROGATE_CONTROL]);
    }

    /**
     * @throws Exception
     */
    public function testAddsSurrogateKeyWhenSet(): void
    {
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getSurrogateKey')
            ->willReturn('get-historical-climate-data');

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        self::assertSame('get-historical-climate-data', $headers[ResponseInterface::HEADER_KEY_SURROGATE_KEY]);
    }

    /**
     * @throws Exception
     */
    public function testBrowserTtlAloneCachesNothing(): void
    {
        // Without a CDN TTL there is no caching to split, so the browser TTL
        // has nothing to apply to and no header is written at all.
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getUseCacheTtl')
            ->willReturn(null);
        $functionConfig->method('getUseBrowserCacheTtl')
            ->willReturn(0);

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        self::assertArrayNotHasKey(ResponseInterface::HEADER_KEY_CACHE_CONTROL, $headers);
    }

    /**
     * @throws Exception
     */
    public function testBrowserTtlCanSimplyBeShorterThanTheCdnTtl(): void
    {
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getUseCacheTtl')
            ->willReturn(3600);
        $functionConfig->method('getUseBrowserCacheTtl')
            ->willReturn(60);
        $functionConfig->method('getUseCacheButRequestTtl')
            ->willReturn(7200);
        $functionConfig->method('getUseCacheIfErrorTtl')
            ->willReturn(259200);

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        // No must-revalidate: a non-zero max-age already expires on its own.
        self::assertSame('s-maxage=3600, max-age=60', $headers[ResponseInterface::HEADER_KEY_CACHE_CONTROL]);
        self::assertSame('max-age=3600, stale-while-revalidate=7200, stale-if-error=259200', $headers[ResponseInterface::HEADER_KEY_SURROGATE_CONTROL]);
    }

    /**
     * @throws Exception
     */
    public function testBrowserTtlIsOnlySplitOutWhenConfigured(): void
    {
        // The guard for every other function that shares this library: with no
        // USE_BROWSER_CACHE_TTL set, the headers are exactly what they were
        // before the setting existed.
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getUseCacheTtl')
            ->willReturn(900);
        $functionConfig->method('getUseBrowserCacheTtl')
            ->willReturn(null);
        $functionConfig->method('getUseCacheButRequestTtl')
            ->willReturn(600);
        $functionConfig->method('getUseCacheIfErrorTtl')
            ->willReturn(3600);

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        self::assertSame('s-maxage=900, max-age=900, stale-while-revalidate=600, stale-if-error=3600', $headers[ResponseInterface::HEADER_KEY_CACHE_CONTROL]);
        self::assertSame('max-age=900, stale-while-revalidate=600, stale-if-error=3600', $headers[ResponseInterface::HEADER_KEY_SURROGATE_CONTROL]);
    }

    /**
     * @throws Exception
     */
    public function testBrowserTtlOfZeroMakesTheBrowserRevalidateEveryTime(): void
    {
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getUseCacheTtl')
            ->willReturn(3600);
        $functionConfig->method('getUseBrowserCacheTtl')
            ->willReturn(0);
        $functionConfig->method('getUseCacheButRequestTtl')
            ->willReturn(7200);
        $functionConfig->method('getUseCacheIfErrorTtl')
            ->willReturn(259200);

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        // The stale directives carry no s- prefix, so leaving them on
        // Cache-Control would let a browser serve a days-old body of its own
        // accord and undo the whole point of revalidating. They stay on
        // Surrogate-Control, which is what the CDN reads and what keeps the
        // CDN's own resilience intact.
        self::assertSame('s-maxage=3600, max-age=0, must-revalidate', $headers[ResponseInterface::HEADER_KEY_CACHE_CONTROL]);
        self::assertSame('max-age=3600, stale-while-revalidate=7200, stale-if-error=259200', $headers[ResponseInterface::HEADER_KEY_SURROGATE_CONTROL]);
    }

    /**
     * @throws Exception
     */
    public function testFailureLeavesHeadersUnchanged(): void
    {
        $functionConfig = self::createStub(FunctionConfigInterface::class);

        $builder = new CacheHeaderBuilder();

        self::assertSame(ResponseInterface::HEADERS, $builder->build(ResponseInterface::HEADERS, $functionConfig, false));
    }

    public function testNoFunctionConfigLeavesHeadersUnchanged(): void
    {
        $builder = new CacheHeaderBuilder();

        self::assertSame(ResponseInterface::HEADERS, $builder->build(ResponseInterface::HEADERS, null, true));
    }

    /**
     * @throws Exception
     */
    public function testNoTtlsAddsNoCacheHeaders(): void
    {
        $functionConfig = self::createStub(FunctionConfigInterface::class);
        $functionConfig->method('getUseCacheTtl')
            ->willReturn(null);
        $functionConfig->method('getUseCacheButRequestTtl')
            ->willReturn(null);
        $functionConfig->method('getUseCacheIfErrorTtl')
            ->willReturn(null);

        $builder = new CacheHeaderBuilder();

        $headers = $builder->build(ResponseInterface::HEADERS, $functionConfig, true);

        self::assertArrayNotHasKey(ResponseInterface::HEADER_KEY_CACHE_CONTROL, $headers);
        self::assertArrayNotHasKey(ResponseInterface::HEADER_KEY_SURROGATE_CONTROL, $headers);
        self::assertArrayNotHasKey(ResponseInterface::HEADER_KEY_SURROGATE_KEY, $headers);
    }
}
