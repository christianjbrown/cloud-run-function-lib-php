<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

final class AllowLocalOriginsApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if ('true' !== ($env[FunctionConfigTransformerInterface::ENV_ALLOW_LOCAL_ORIGINS] ?? null)) {
            return $config;
        }

        return $config->withAllowLocalOrigins(true);
    }
}
