<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

interface JsonResponseFactoryInterface
{
    public function error(?FunctionConfigInterface $functionConfig, ?string $error = null, int $statusCode = JsonErrorResponseInterface::DEFAULT_ERROR_STATUS_CODE, ?string $requestOrigin = null): ResponseInterface;

    /**
     * @phpstan-param mixed[] $data
     */
    public function success(?FunctionConfigInterface $functionConfig, array $data = [], int $statusCode = ResponseInterface::STATUS_OK, ?string $requestOrigin = null): ResponseInterface;
}
