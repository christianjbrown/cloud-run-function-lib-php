<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use Symfony\Component\Clock\NativeClock;

/**
 * The composition root: the one place that wires the default object graph.
 */
final class CloudRunFunctionFactory implements CloudRunFunctionFactoryInterface
{
    public function create(DataProviderInterface $dataProvider, FunctionConfigInterface $functionConfig): CloudRunFunctionInterface
    {
        return new CloudRunFunction(
            $dataProvider,
            $functionConfig,
            new HeaderRequestAuthorizer(),
            new JsonResponseFactory(
                new ResponseBodyBuilder(),
                new CorsHeaderBuilder(new AllowOriginResolver()),
                new CacheHeaderBuilder(),
                new NativeClock(),
            ),
        );
    }

    public function createConfigTransformer(): FunctionConfigTransformerInterface
    {
        return new FunctionConfigTransformer([
            new AllowLocalOriginsApplier(),
            new AllowUnauthenticatedApplier(),
            new DebugApplier(),
            new RequiredHeaderKeyApplier(),
            new RequiredHeaderValueApplier(),
            new RequiredOriginApplier(),
            new SurrogateKeyApplier(),
            new UseBrowserCacheTtlApplier(),
            new UseCacheTtlApplier(),
            new UseCacheButRequestTtlApplier(),
            new UseCacheIfErrorTtlApplier(),
        ]);
    }

    /**
     * @phpstan-param mixed[] $env
     */
    public function createFromEnvironment(DataProviderInterface $dataProvider, array $env): CloudRunFunctionInterface
    {
        return $this->create($dataProvider, $this->createConfigTransformer()->transform($env));
    }
}
