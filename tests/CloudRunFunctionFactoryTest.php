<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction\Tests;

use ChristianBrown\CloudRunFunction\AllowLocalOriginsApplier;
use ChristianBrown\CloudRunFunction\AllowOriginResolver;
use ChristianBrown\CloudRunFunction\AllowUnauthenticatedApplier;
use ChristianBrown\CloudRunFunction\CacheHeaderBuilder;
use ChristianBrown\CloudRunFunction\CloudRunFunction;
use ChristianBrown\CloudRunFunction\CloudRunFunctionFactory;
use ChristianBrown\CloudRunFunction\CorsHeaderBuilder;
use ChristianBrown\CloudRunFunction\DataProviderInterface;
use ChristianBrown\CloudRunFunction\DebugApplier;
use ChristianBrown\CloudRunFunction\FunctionConfig;
use ChristianBrown\CloudRunFunction\FunctionConfigTransformer;
use ChristianBrown\CloudRunFunction\HeaderRequestAuthorizer;
use ChristianBrown\CloudRunFunction\JsonResponse;
use ChristianBrown\CloudRunFunction\JsonResponseFactory;
use ChristianBrown\CloudRunFunction\RequiredHeaderKeyApplier;
use ChristianBrown\CloudRunFunction\RequiredHeaderValueApplier;
use ChristianBrown\CloudRunFunction\RequiredOriginApplier;
use ChristianBrown\CloudRunFunction\ResponseBodyBuilder;
use ChristianBrown\CloudRunFunction\SurrogateKeyApplier;
use ChristianBrown\CloudRunFunction\UseBrowserCacheTtlApplier;
use ChristianBrown\CloudRunFunction\UseCacheButRequestTtlApplier;
use ChristianBrown\CloudRunFunction\UseCacheIfErrorTtlApplier;
use ChristianBrown\CloudRunFunction\UseCacheTtlApplier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(AllowLocalOriginsApplier::class)]
#[CoversClass(AllowOriginResolver::class)]
#[CoversClass(AllowUnauthenticatedApplier::class)]
#[CoversClass(CacheHeaderBuilder::class)]
#[CoversClass(CloudRunFunction::class)]
#[CoversClass(CloudRunFunctionFactory::class)]
#[CoversClass(CorsHeaderBuilder::class)]
#[CoversClass(DebugApplier::class)]
#[CoversClass(FunctionConfig::class)]
#[CoversClass(FunctionConfigTransformer::class)]
#[CoversClass(HeaderRequestAuthorizer::class)]
#[CoversClass(JsonResponse::class)]
#[CoversClass(JsonResponseFactory::class)]
#[CoversClass(RequiredHeaderKeyApplier::class)]
#[CoversClass(RequiredHeaderValueApplier::class)]
#[CoversClass(RequiredOriginApplier::class)]
#[CoversClass(ResponseBodyBuilder::class)]
#[CoversClass(SurrogateKeyApplier::class)]
#[CoversClass(UseBrowserCacheTtlApplier::class)]
#[CoversClass(UseCacheButRequestTtlApplier::class)]
#[CoversClass(UseCacheIfErrorTtlApplier::class)]
#[CoversClass(UseCacheTtlApplier::class)]
final class CloudRunFunctionFactoryTest extends TestCase
{
    public function testCreateFromEnvironmentBuildsAWorkingFunction(): void
    {
        $dataProvider = self::createStub(DataProviderInterface::class);
        $dataProvider->method('getData')
            ->willReturn(['hello' => 'world']);
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturn('secret');

        $function = (new CloudRunFunctionFactory())->createFromEnvironment($dataProvider, [
            'K_REVISION' => 'rev-1',
            'REQUIRED_HEADER_KEY' => 'X-Key',
            'REQUIRED_HEADER_VALUE' => 'secret',
            'USE_CACHE_TTL' => '60',
        ]);
        $response = $function->run($request);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('s-maxage=60, max-age=60', $response->getHeaderLine('Cache-Control'));
        $json = json_decode((string) $response->getBody(), true);
        self::assertIsArray($json);
        self::assertSame(['hello' => 'world'], $json['data']);
        self::assertSame('rev-1', $json['version']);
    }
}
