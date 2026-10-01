<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use function is_string;

final class RequiredHeaderValueApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if (empty($env[FunctionConfigTransformerInterface::ENV_REQUIRED_HEADER_VALUE])) {
            return $config;
        }
        $value = $env[FunctionConfigTransformerInterface::ENV_REQUIRED_HEADER_VALUE];
        if (!is_string($value)) {
            return $config;
        }

        return $config->withRequiredHeaderValue($value);
    }
}
