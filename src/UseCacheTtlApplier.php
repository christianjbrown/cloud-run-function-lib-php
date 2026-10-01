<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use function is_numeric;

final class UseCacheTtlApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if (empty($env[FunctionConfigTransformerInterface::ENV_USE_CACHE_TTL])) {
            return $config;
        }
        $value = $env[FunctionConfigTransformerInterface::ENV_USE_CACHE_TTL];
        if (!is_numeric($value)) {
            return $config;
        }

        return $config->withUseCacheTtl((int) $value);
    }
}
