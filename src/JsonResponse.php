<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use GuzzleHttp\Psr7\Response;

/**
 * A finished PSR-7 response. It is data only: JsonResponseFactory decides what
 * goes in it.
 */
final class JsonResponse extends Response implements ResponseInterface
{
}
