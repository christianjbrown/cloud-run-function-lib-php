# Google Cloud Run Function framework

[![CI](https://github.com/christianjbrown/cloud-run-function-lib-php/actions/workflows/ci.yml/badge.svg)](https://github.com/christianjbrown/cloud-run-function-lib-php/actions/workflows/ci.yml)

A strongly-typed PHP framework for building [Google Cloud Run function](https://cloud.google.com/run) HTTP endpoints that return a **consistent JSON envelope**. You write the business logic; the library handles header-based authorization, CORS, CDN cache-control headers, and uniform success/error responses.

It is built around [PSR-7](https://www.php-fig.org/psr/psr-7/): you hand it a `ServerRequestInterface` and it returns a `ResponseInterface`. Configuration is read straight from your Cloud Run environment variables.

- **Uniform envelope** — every response carries `success`, `timestamp_unix`, and `timestamp_iso8601`, plus `data`, `version` (the Cloud Run revision), or `error` as appropriate.
- **Header authorization** — optionally require a header key/value before running your handler.
- **CORS + caching** — `Access-Control-*`, `Vary`, `Cache-Control`, and `Surrogate-Control` headers derived from config.
- **Safe error handling** — user-friendly exceptions surface their message; anything else returns a generic error unless `DEBUG` is on.



## :heavy_check_mark: Prerequisites

- [Git](https://git-scm.com/)
- [PHP](https://www.php.net/) 8.5 or higher (8.x)
- [Composer](https://getcomposer.org/)

:bulb: If you're on MacOS and have [Homebrew](https://brew.sh/), PHP and Composer will install with `brew install composer`.



## :building_construction: Installation

For your composer-enabled project:

```bash
composer require christianjbrown/cloud-run-function-lib
```



## :computer: Usage

Implement `DataProviderInterface` with your endpoint's logic — it receives the PSR-7 request and returns an array that becomes the response `data`:

```php
use ChristianBrown\CloudRunFunction\DataProviderInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MyDataProvider implements DataProviderInterface
{
    /**
     * @return mixed[]
     */
    public function getData(ServerRequestInterface $request): array
    {
        // Your business logic. Throw a UserFriendlyExceptionInterface to return
        // a specific message to the client; any other Throwable is hidden unless DEBUG is on.
        return ['hello' => 'world'];
    }
}
```

Build a `FunctionConfig` from your Cloud Run environment variables with `FunctionConfigTransformer`, wire it into a `CloudRunFunction`, and run the request:

```php
use ChristianBrown\CloudRunFunction\CloudRunFunction;
use ChristianBrown\CloudRunFunction\FunctionConfigTransformer;

$config = (new FunctionConfigTransformer())->transform($_ENV);

$cloudFunction = new CloudRunFunction(new MyDataProvider(), $config);

$response = $cloudFunction->run($request); // Psr\Http\Message\ResponseInterface
```

`$response` is a PSR-7 response ready to emit (e.g. with `guzzlehttp/psr7`'s HTTP factories or your Cloud Run function's runtime).

### Environment variables

`FunctionConfigTransformer::transform()` reads these keys (only `K_REVISION` is required — Cloud Run sets it automatically):

| Variable | Purpose |
| --- | --- |
| `K_REVISION` | **Required.** The revision id, surfaced as `version` in the response. |
| `DEBUG` | `"true"` to return raw exception messages instead of a generic error. |
| `REQUIRED_HEADER_KEY` / `REQUIRED_HEADER_VALUE` | Require this header on the request, else `401`. |
| `REQUIRED_ORIGIN` | Value for `Access-Control-Allow-Origin` (enables the `Vary` header). |
| `USE_CACHE_TTL` | `s-maxage` / `max-age` seconds for successful responses. |
| `USE_BROWSER_CACHE_TTL` | `max-age` seconds for browsers only, when that should differ from the CDN's. See below. |
| `USE_CACHE_BUT_REQUEST_TTL` | `stale-while-revalidate` seconds. |
| `USE_CACHE_IF_ERROR_TTL` | `stale-if-error` seconds. |

#### Letting a purge reach visitors

By default the browser and the CDN are given the same TTL, so `USE_CACHE_TTL=3600`
produces:

```
Cache-Control:     s-maxage=3600, max-age=3600, stale-while-revalidate=..., stale-if-error=...
Surrogate-Control: max-age=3600, stale-while-revalidate=..., stale-if-error=...
```

That `max-age` is what makes a surrogate-key purge look like it did nothing: the
CDN drops its copy, but a visitor who loaded the page in the last hour keeps
theirs. Set `USE_BROWSER_CACHE_TTL` to split the two. With `0`:

```
Cache-Control:     s-maxage=3600, max-age=0, must-revalidate
Surrogate-Control: max-age=3600, stale-while-revalidate=..., stale-if-error=...
```

The browser now revalidates on every page load, which the CDN answers from its
own cache, so a purge is visible immediately. Fastly reads `Surrogate-Control`
in preference to `Cache-Control` and strips it before the response reaches the
client, so the CDN's own TTL and its stale-while-revalidate / stale-if-error
resilience are untouched.

Note what is **not** in that `Cache-Control`: the stale directives carry no `s-`
prefix, so leaving them there would let a browser serve a body days old of its
own accord and undo the point of revalidating. They are emitted on
`Surrogate-Control` only whenever `USE_BROWSER_CACHE_TTL` is set.

Leave the variable unset and the headers are exactly as they were before it
existed.

### Response shape

A successful response:

```json
{
    "data": { "hello": "world" },
    "success": true,
    "timestamp_iso8601": "2026-07-15T12:00:00+00:00",
    "timestamp_unix": 1784030400,
    "version": "my-service-00001-abc"
}
```

An error response omits `data` and adds `error`:

```json
{
    "error": "Not authorized",
    "success": false,
    "timestamp_iso8601": "2026-07-15T12:00:00+00:00",
    "timestamp_unix": 1784030400,
    "version": "my-service-00001-abc"
}
```

## :rotating_light: Error handling

Inside your `DataProviderInterface::getData()`, throwing an exception that implements [`christianjbrown/user-friendly-exception`](https://github.com/christianjbrown/user-friendly-exception-php)'s `UserFriendlyExceptionInterface` returns its message to the client (HTTP 500). Any other `Throwable` returns a generic `"An unhandled error occurred"` message — unless `DEBUG` is enabled, in which case the raw message is returned to aid debugging. A failed authorization check short-circuits with `"Not authorized"` (HTTP 401) before your handler runs.

## :page_facing_up: License

Released under the [MIT License](LICENSE).
