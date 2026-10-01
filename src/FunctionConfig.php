<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

final class FunctionConfig implements FunctionConfigInterface
{
    public function __construct(
        private readonly string $kRevision,
        private readonly bool $allowLocalOrigins = false,
        private readonly bool $allowUnauthenticated = false,
        private readonly bool $debug = false,
        private readonly ?string $requiredHeaderKey = null,
        private readonly ?string $requiredHeaderValue = null,
        private readonly ?string $requiredOrigin = null,
        private readonly ?string $surrogateKey = null,
        private readonly ?int $useBrowserCacheTtl = null,
        private readonly ?int $useCacheButRequestTtl = null,
        private readonly ?int $useCacheIfErrorTtl = null,
        private readonly ?int $useCacheTtl = null,
    ) {
    }

    public function getAllowLocalOrigins(): bool
    {
        return $this->allowLocalOrigins;
    }

    public function getAllowUnauthenticated(): bool
    {
        return $this->allowUnauthenticated;
    }

    public function getDebug(): bool
    {
        return $this->debug;
    }

    public function getKrevision(): string
    {
        return $this->kRevision;
    }

    public function getRequiredHeaderKey(): ?string
    {
        return $this->requiredHeaderKey;
    }

    public function getRequiredHeaderValue(): ?string
    {
        return $this->requiredHeaderValue;
    }

    public function getRequiredOrigin(): ?string
    {
        return $this->requiredOrigin;
    }

    public function getSurrogateKey(): ?string
    {
        return $this->surrogateKey;
    }

    public function getUseBrowserCacheTtl(): ?int
    {
        return $this->useBrowserCacheTtl;
    }

    public function getUseCacheButRequestTtl(): ?int
    {
        return $this->useCacheButRequestTtl;
    }

    public function getUseCacheIfErrorTtl(): ?int
    {
        return $this->useCacheIfErrorTtl;
    }

    public function getUseCacheTtl(): ?int
    {
        return $this->useCacheTtl;
    }

    public function withAllowLocalOrigins(bool $value): self
    {
        return clone ($this, ['allowLocalOrigins' => $value]);
    }

    public function withAllowUnauthenticated(bool $value): self
    {
        return clone ($this, ['allowUnauthenticated' => $value]);
    }

    public function withDebug(bool $value): self
    {
        return clone ($this, ['debug' => $value]);
    }

    public function withRequiredHeaderKey(?string $value): self
    {
        return clone ($this, ['requiredHeaderKey' => $value]);
    }

    public function withRequiredHeaderValue(?string $value): self
    {
        return clone ($this, ['requiredHeaderValue' => $value]);
    }

    public function withRequiredOrigin(?string $value): self
    {
        return clone ($this, ['requiredOrigin' => $value]);
    }

    public function withSurrogateKey(?string $value): self
    {
        return clone ($this, ['surrogateKey' => $value]);
    }

    public function withUseBrowserCacheTtl(?int $value): self
    {
        return clone ($this, ['useBrowserCacheTtl' => $value]);
    }

    public function withUseCacheButRequestTtl(?int $value): self
    {
        return clone ($this, ['useCacheButRequestTtl' => $value]);
    }

    public function withUseCacheIfErrorTtl(?int $value): self
    {
        return clone ($this, ['useCacheIfErrorTtl' => $value]);
    }

    public function withUseCacheTtl(?int $value): self
    {
        return clone ($this, ['useCacheTtl' => $value]);
    }
}
