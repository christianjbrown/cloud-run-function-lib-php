<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use function is_string;

final class RequiredOriginApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if (empty($env[FunctionConfigTransformerInterface::ENV_REQUIRED_ORIGIN])) {
            return $config;
        }
        $value = $env[FunctionConfigTransformerInterface::ENV_REQUIRED_ORIGIN];
        if (!is_string($value)) {
            return $config;
        }

        return $config->withRequiredOrigin($value);
    }
}
