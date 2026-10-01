<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use Psr\Clock\ClockInterface;

final class JsonResponseFactory implements JsonResponseFactoryInterface
{
    public function __construct(
        private readonly ResponseBodyBuilderInterface $bodyBuilder,
        private readonly CorsHeaderBuilderInterface $corsHeaderBuilder,
        private readonly CacheHeaderBuilderInterface $cacheHeaderBuilder,
        private readonly ClockInterface $clock,
    ) {
    }

    public function error(?FunctionConfigInterface $functionConfig, ?string $error = null, int $statusCode = JsonErrorResponseInterface::DEFAULT_ERROR_STATUS_CODE, ?string $requestOrigin = null): ResponseInterface
    {
        return $this->build($functionConfig, [], false, $error, $statusCode, $requestOrigin);
    }

    /**
     * @phpstan-param mixed[] $data
     */
    public function success(?FunctionConfigInterface $functionConfig, array $data = [], int $statusCode = ResponseInterface::STATUS_OK, ?string $requestOrigin = null): ResponseInterface
    {
        return $this->build($functionConfig, $data, true, null, $statusCode, $requestOrigin);
    }

    /**
     * @phpstan-param mixed[] $data
     */
    private function build(?FunctionConfigInterface $functionConfig, array $data, bool $success, ?string $error, int $statusCode, ?string $requestOrigin): ResponseInterface
    {
        $bodyJson = $this->bodyBuilder->build($data, $functionConfig, $success, $error, $this->clock->now()->getTimestamp());
        [$body, $success, $statusCode] = $this->bodyBuilder->encode($bodyJson, $success, $statusCode);

        $headers = $this->corsHeaderBuilder->build(ResponseInterface::HEADERS, $functionConfig, $requestOrigin);
        $headers = $this->cacheHeaderBuilder->build($headers, $functionConfig, $success);

        return new JsonResponse($statusCode, $headers, $body);
    }
}
