<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

interface FunctionConfigApplierInterface
{
    /**
     * Returns the config with this applier's one setting taken from the
     * environment, or the same config when the variable is absent or invalid.
     *
     * @phpstan-param mixed[] $env
     */
    public function apply(FunctionConfigInterface $config, array $env): FunctionConfigInterface;
}
