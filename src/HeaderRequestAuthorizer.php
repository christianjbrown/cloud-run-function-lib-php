<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use Psr\Http\Message\ServerRequestInterface;

use function hash_equals;

final class HeaderRequestAuthorizer implements RequestAuthorizerInterface
{
    public function isAuthorized(ServerRequestInterface $request, FunctionConfigInterface $config): bool
    {
        $requiredHeaderKey = (string) $config->getRequiredHeaderKey();
        $requiredHeaderValue = (string) $config->getRequiredHeaderValue();

        if ('' === $requiredHeaderKey) {
            // Neither part of the gate is configured: this is only allowed when
            // the function is explicitly opted in to unauthenticated access, so
            // a dropped/emptied secret fails closed instead of silently opening.
            if ('' === $requiredHeaderValue) {
                return $config->getAllowUnauthenticated();
            }

            // Only the value is configured: a partial gate is a misconfiguration
            // and is treated as deny.
            return false;
        }

        // Only the key is configured: a partial gate is a misconfiguration and
        // is treated as deny.
        if ('' === $requiredHeaderValue) {
            return false;
        }

        return hash_equals($requiredHeaderValue, $request->getHeaderLine($requiredHeaderKey));
    }
}
