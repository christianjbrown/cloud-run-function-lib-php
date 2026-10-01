<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use function is_numeric;

/**
 * How long a browser may hold the response, when that should differ from how
 * long the CDN may.
 *
 * Checked with isset rather than empty, unlike every other TTL: zero is the
 * value this setting exists to express ("revalidate every time"), and
 * empty('0') is true, so an empty check would silently discard exactly the case
 * worth configuring.
 */
final class UseBrowserCacheTtlApplier implements FunctionConfigApplierInterface
{
    /**
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface
    {
        if (!isset($env[FunctionConfigTransformerInterface::ENV_USE_BROWSER_CACHE_TTL])) {
            return $config;
        }
        $value = $env[FunctionConfigTransformerInterface::ENV_USE_BROWSER_CACHE_TTL];
        if (!is_numeric($value)) {
            return $config;
        }

        return $config->withUseBrowserCacheTtl((int) $value);
    }
}
