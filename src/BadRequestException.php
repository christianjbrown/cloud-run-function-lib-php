<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use RuntimeException;

/**
 * A UserFriendlyException whose cause is the request rather than the service,
 * so it answers 400 instead of 500. The message is shown to the caller, so it
 * should say what was wrong with the request.
 */
final class BadRequestException extends RuntimeException implements BadRequestExceptionInterface
{
}
