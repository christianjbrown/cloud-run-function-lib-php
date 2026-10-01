<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use ChristianBrown\UserFriendlyException\UserFriendlyExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function error_log;

final class CloudRunFunction implements CloudRunFunctionInterface
{
    public function __construct(
        private readonly DataProviderInterface $dataProvider,
        private readonly FunctionConfigInterface $functionConfig,
        private readonly RequestAuthorizerInterface $authorizer,
        private readonly JsonResponseFactoryInterface $responseFactory,
    ) {
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $requestOrigin = $request->getHeaderLine(ResponseInterface::HEADER_KEY_ORIGIN);

        try {
            return $this->handle($request, $requestOrigin);
        } catch (BadRequestExceptionInterface $exception) {
            return $this->responseFactory->error($this->functionConfig, $exception->getMessage(), ResponseInterface::STATUS_BAD_REQUEST, $requestOrigin);
        } catch (UserFriendlyExceptionInterface $exception) {
            return $this->responseFactory->error($this->functionConfig, $exception->getMessage(), JsonErrorResponseInterface::DEFAULT_ERROR_STATUS_CODE, $requestOrigin);
        } catch (Throwable $exception) {
            return $this->buildUnhandledResponse($exception, $requestOrigin);
        }
    }

    private function buildUnhandledResponse(Throwable $exception, string $requestOrigin): ResponseInterface
    {
        // This is the only branch that discards what actually went wrong: the
        // caller gets a generic message and, with DEBUG off, the exception is
        // otherwise never recorded anywhere. Write it to stderr so Cloud Logging
        // keeps the cause against the failing request, giving the 5xx alert
        // something to read.
        error_log((string) $exception);

        if ($this->functionConfig->getDebug()) {
            return $this->responseFactory->error($this->functionConfig, $exception->getMessage(), JsonErrorResponseInterface::DEFAULT_ERROR_STATUS_CODE, $requestOrigin);
        }

        return $this->responseFactory->error($this->functionConfig, self::ERROR_UNHANDLED, JsonErrorResponseInterface::DEFAULT_ERROR_STATUS_CODE, $requestOrigin);
    }

    private function handle(ServerRequestInterface $request, string $requestOrigin): ResponseInterface
    {
        if (!$this->authorizer->isAuthorized($request, $this->functionConfig)) {
            return $this->responseFactory->error($this->functionConfig, self::ERROR_NOT_AUTHORIZED, ResponseInterface::STATUS_UNAUTHORIZED, $requestOrigin);
        }

        $data = $this->dataProvider->getData($request);

        return $this->responseFactory->success($this->functionConfig, $data, ResponseInterface::STATUS_OK, $requestOrigin);
    }
}
