<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

final class AllowUnauthenticatedApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if ('true' !== ($env[FunctionConfigTransformerInterface::ENV_ALLOW_UNAUTHENTICATED] ?? null)) {
            return $config;
        }

        return $config->withAllowUnauthenticated(true);
    }
}
