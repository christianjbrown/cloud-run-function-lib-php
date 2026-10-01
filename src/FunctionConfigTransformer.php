<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use RuntimeException;

use function array_reduce;
use function is_string;
use function iterator_to_array;
use function sprintf;

final class FunctionConfigTransformer implements FunctionConfigTransformerInterface
{
    /**
     * @param iterable<FunctionConfigApplierInterface> $appliers
     */
    public function __construct(private readonly iterable $appliers)
    {
    }

    /**
     * @param mixed[] $env
     */
    public function transform(array $env): FunctionConfigInterface
    {
        if (empty($env[self::ENV_K_REVISION])) {
            throw new RuntimeException(sprintf('%s not set or not a string', self::ENV_K_REVISION));
        }
        if (!is_string($env[self::ENV_K_REVISION])) {
            throw new RuntimeException(sprintf('%s not set or not a string', self::ENV_K_REVISION));
        }

        return array_reduce(
            iterator_to_array($this->appliers, false),
            static fn (FunctionConfigInterface $config, FunctionConfigApplierInterface $applier): FunctionConfigInterface => $applier->apply($config, $env),
            new FunctionConfig($env[self::ENV_K_REVISION]),
        );
    }
}
