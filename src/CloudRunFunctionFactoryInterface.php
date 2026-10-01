<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

interface CloudRunFunctionFactoryInterface
{
    public function create(DataProviderInterface $dataProvider, FunctionConfigInterface $functionConfig): CloudRunFunctionInterface;

    public function createConfigTransformer(): FunctionConfigTransformerInterface;

    /**
     * Builds the function from the Cloud Run environment variables in one call.
     *
     * @phpstan-param mixed[] $env
     */
    public function createFromEnvironment(DataProviderInterface $dataProvider, array $env): CloudRunFunctionInterface;
}
