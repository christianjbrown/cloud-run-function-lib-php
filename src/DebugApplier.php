<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

final class DebugApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if ('true' !== ($env[FunctionConfigTransformerInterface::ENV_DEBUG] ?? null)) {
            return $config;
        }

        return $config->withDebug(true);
    }
}
