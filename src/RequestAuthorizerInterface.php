<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use Psr\Http\Message\ServerRequestInterface;

interface RequestAuthorizerInterface
{
    public function isAuthorized(ServerRequestInterface $request, FunctionConfigInterface $config): bool;
}
