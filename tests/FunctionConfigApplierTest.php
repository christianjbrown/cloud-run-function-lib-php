<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction\Tests;

use ChristianBrown\CloudRunFunction\AllowLocalOriginsApplier;
use ChristianBrown\CloudRunFunction\AllowUnauthenticatedApplier;
use ChristianBrown\CloudRunFunction\DebugApplier;
use ChristianBrown\CloudRunFunction\FunctionConfig;
use ChristianBrown\CloudRunFunction\FunctionConfigApplierInterface;
use ChristianBrown\CloudRunFunction\RequiredHeaderKeyApplier;
use ChristianBrown\CloudRunFunction\RequiredHeaderValueApplier;
use ChristianBrown\CloudRunFunction\RequiredOriginApplier;
use ChristianBrown\CloudRunFunction\SurrogateKeyApplier;
use ChristianBrown\CloudRunFunction\UseBrowserCacheTtlApplier;
use ChristianBrown\CloudRunFunction\UseCacheButRequestTtlApplier;
use ChristianBrown\CloudRunFunction\UseCacheIfErrorTtlApplier;
use ChristianBrown\CloudRunFunction\UseCacheTtlApplier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(FunctionConfig::class)]
#[CoversClass(AllowLocalOriginsApplier::class)]
#[CoversClass(AllowUnauthenticatedApplier::class)]
#[CoversClass(DebugApplier::class)]
#[CoversClass(RequiredHeaderKeyApplier::class)]
#[CoversClass(RequiredHeaderValueApplier::class)]
#[CoversClass(RequiredOriginApplier::class)]
#[CoversClass(SurrogateKeyApplier::class)]
#[CoversClass(UseBrowserCacheTtlApplier::class)]
#[CoversClass(UseCacheButRequestTtlApplier::class)]
#[CoversClass(UseCacheIfErrorTtlApplier::class)]
#[CoversClass(UseCacheTtlApplier::class)]
final class FunctionConfigApplierTest extends TestCase
{
    #[DataProvider('provideBooleanSetsOnlyOnTrueCases')]
    public function testBooleanSetsOnlyOnTrue(FunctionConfigApplierInterface $applier, string $key, string $getter): void
    {
        $config = new FunctionConfig('rev');

        self::assertTrue($applier->apply($config, [$key => 'true'])->{$getter}());
        self::assertFalse($applier->apply($config, [$key => 'false'])->{$getter}());
        self::assertFalse($applier->apply($config, [])->{$getter}());
    }

    /**
     * @return iterable<string, array{FunctionConfigApplierInterface, string, string}>
     */
    public static function provideBooleanSetsOnlyOnTrueCases(): iterable
    {
        yield 'allow local origins' => [new AllowLocalOriginsApplier(), 'ALLOW_LOCAL_ORIGINS', 'getAllowLocalOrigins'];
        yield 'allow unauthenticated' => [new AllowUnauthenticatedApplier(), 'ALLOW_UNAUTHENTICATED', 'getAllowUnauthenticated'];
        yield 'debug' => [new DebugApplier(), 'DEBUG', 'getDebug'];
    }

    public function testBrowserTtlKeepsZeroAndOthersDiscardIt(): void
    {
        $config = new FunctionConfig('rev');

        self::assertSame(0, (new UseBrowserCacheTtlApplier())->apply($config, ['USE_BROWSER_CACHE_TTL' => '0'])->getUseBrowserCacheTtl());
        self::assertNull((new UseCacheTtlApplier())->apply($config, ['USE_CACHE_TTL' => '0'])->getUseCacheTtl());
    }

    #[DataProvider('provideIntSetsOnlyOnNumericCases')]
    public function testIntSetsOnlyOnNumeric(FunctionConfigApplierInterface $applier, string $key, string $getter): void
    {
        $config = new FunctionConfig('rev');

        self::assertSame(60, $applier->apply($config, [$key => '60'])->{$getter}());
        self::assertNull($applier->apply($config, [$key => 'abc'])->{$getter}());
        self::assertNull($applier->apply($config, [])->{$getter}());
    }

    /**
     * @return iterable<string, array{FunctionConfigApplierInterface, string, string}>
     */
    public static function provideIntSetsOnlyOnNumericCases(): iterable
    {
        yield 'use cache ttl' => [new UseCacheTtlApplier(), 'USE_CACHE_TTL', 'getUseCacheTtl'];
        yield 'use cache but request ttl' => [new UseCacheButRequestTtlApplier(), 'USE_CACHE_BUT_REQUEST_TTL', 'getUseCacheButRequestTtl'];
        yield 'use cache if error ttl' => [new UseCacheIfErrorTtlApplier(), 'USE_CACHE_IF_ERROR_TTL', 'getUseCacheIfErrorTtl'];
        yield 'use browser cache ttl' => [new UseBrowserCacheTtlApplier(), 'USE_BROWSER_CACHE_TTL', 'getUseBrowserCacheTtl'];
    }

    #[DataProvider('provideStringSetsOnlyOnNonEmptyStringCases')]
    public function testStringSetsOnlyOnNonEmptyString(FunctionConfigApplierInterface $applier, string $key, string $getter): void
    {
        $config = new FunctionConfig('rev');

        self::assertSame('value', $applier->apply($config, [$key => 'value'])->{$getter}());
        self::assertNull($applier->apply($config, [$key => ''])->{$getter}());
        self::assertNull($applier->apply($config, [$key => 123])->{$getter}());
        self::assertNull($applier->apply($config, [])->{$getter}());
    }

    /**
     * @return iterable<string, array{FunctionConfigApplierInterface, string, string}>
     */
    public static function provideStringSetsOnlyOnNonEmptyStringCases(): iterable
    {
        yield 'required header key' => [new RequiredHeaderKeyApplier(), 'REQUIRED_HEADER_KEY', 'getRequiredHeaderKey'];
        yield 'required header value' => [new RequiredHeaderValueApplier(), 'REQUIRED_HEADER_VALUE', 'getRequiredHeaderValue'];
        yield 'required origin' => [new RequiredOriginApplier(), 'REQUIRED_ORIGIN', 'getRequiredOrigin'];
        yield 'surrogate key' => [new SurrogateKeyApplier(), 'SURROGATE_KEY', 'getSurrogateKey'];
    }
}
