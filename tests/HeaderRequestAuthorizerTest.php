<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction\Tests;

use ChristianBrown\CloudRunFunction\FunctionConfig;
use ChristianBrown\CloudRunFunction\HeaderRequestAuthorizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(FunctionConfig::class)]
#[CoversClass(HeaderRequestAuthorizer::class)]
final class HeaderRequestAuthorizerTest extends TestCase
{
    #[TestWith([null, null, false, 'ignored', false])]
    #[TestWith([null, null, true, 'ignored', true])]
    #[TestWith(['', '', true, 'ignored', true])]
    #[TestWith([null, 'secret', true, 'secret', false])]
    #[TestWith(['X-Key', null, true, 'secret', false])]
    #[TestWith(['X-Key', 'secret', false, 'secret', true])]
    #[TestWith(['X-Key', 'secret', false, 'wrong', false])]
    #[TestWith(['X-Key', 'secret', false, '', false])]
    public function testIsAuthorized(?string $key, ?string $value, bool $allowUnauthenticated, string $sent, bool $expected): void
    {
        $config = (new FunctionConfig('rev'))
            ->withRequiredHeaderKey($key)
            ->withRequiredHeaderValue($value)
            ->withAllowUnauthenticated($allowUnauthenticated);
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getHeaderLine')
            ->willReturn($sent);

        self::assertSame($expected, (new HeaderRequestAuthorizer())->isAuthorized($request, $config));
    }
}
