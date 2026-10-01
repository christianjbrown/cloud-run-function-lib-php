<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

interface FunctionConfigInterface
{
    public function getAllowLocalOrigins(): bool;

    public function getAllowUnauthenticated(): bool;

    public function getDebug(): bool;

    public function getKrevision(): string;

    public function getRequiredHeaderKey(): ?string;

    public function getRequiredHeaderValue(): ?string;

    public function getRequiredOrigin(): ?string;

    public function getSurrogateKey(): ?string;

    public function getUseBrowserCacheTtl(): ?int;

    public function getUseCacheButRequestTtl(): ?int;

    public function getUseCacheIfErrorTtl(): ?int;

    public function getUseCacheTtl(): ?int;

    public function withAllowLocalOrigins(bool $value): self;

    public function withAllowUnauthenticated(bool $value): self;

    public function withDebug(bool $value): self;

    public function withRequiredHeaderKey(?string $value): self;

    public function withRequiredHeaderValue(?string $value): self;

    public function withRequiredOrigin(?string $value): self;

    public function withSurrogateKey(?string $value): self;

    public function withUseBrowserCacheTtl(?int $value): self;

    public function withUseCacheButRequestTtl(?int $value): self;

    public function withUseCacheIfErrorTtl(?int $value): self;

    public function withUseCacheTtl(?int $value): self;
}
