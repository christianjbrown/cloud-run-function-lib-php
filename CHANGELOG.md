# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- The archive Composer installs no longer contains the tests, CI and editor configuration, `CLAUDE.md` or other development-only files, only the library itself, its README, CHANGELOG and LICENSE.

## [2.0.0] - 2026-10-01

### Added

- `CloudRunFunctionFactory`, the composition root. `create($dataProvider, $config)` builds a ready
  `CloudRunFunction` with every default wired, `createConfigTransformer()` builds the environment
  transformer and `createFromEnvironment($dataProvider, $_ENV)` does both in one call.
- `JsonResponseFactory`, which composes the body, CORS and cache builders with a PSR-20 clock and
  produces the success and error responses. Timestamps are read from the clock when each response is
  built.
- `RequestAuthorizerInterface` with a `HeaderRequestAuthorizer` implementation, so the authorization
  policy can be replaced.
- One `FunctionConfigApplierInterface` implementation per environment variable. A new setting is a
  new applier plus one line in `CloudRunFunctionFactory`.
- Dependencies on `psr/clock` and `symfony/clock`.

### Changed

- `CloudRunFunction` now takes four collaborators: the data provider, the config, a
  `RequestAuthorizerInterface` and a `JsonResponseFactoryInterface`. Build it with
  `CloudRunFunctionFactory` rather than `new`.
- `FunctionConfigTransformer` now takes an iterable of `FunctionConfigApplierInterface`. Get a
  default one from `CloudRunFunctionFactory::createConfigTransformer()`.
- `FunctionConfig` is immutable. Every `setX()` is now `withX()` and returns a new instance, so keep
  the return value. The constructor accepts every setting as an optional named argument.
- `CloudRunFunctionInterface::run()` is declared to return this package's `ResponseInterface`, which
  is what it already returned.

### Removed

- `AbstractJsonResponse`, `JsonSuccessResponse`, `JsonErrorResponse` and `JsonSuccessResponseInterface`.
  Responses come from `JsonResponseFactory`, and are instances of `JsonResponse`.

## [1.0.0] - 2026-10-01

First stable release.

### Added

- `CloudRunFunction`, which takes a PSR-7 request, runs your `DataProviderInterface` and returns a
  PSR-7 response in a consistent JSON envelope: `success`, `timestamp_unix` and `timestamp_iso8601`,
  plus `data`, `version` or `error`.
- Configuration read from the Cloud Run environment variables through `FunctionConfigTransformer`.
- Optional header authorization, so a request without the required header key and value is refused
  before your handler runs.
- CORS, `Vary`, `Cache-Control` and `Surrogate-Control` headers derived from the configuration.
- Safe error handling: a `UserFriendlyExceptionInterface` message reaches the caller, anything else
  returns a generic error unless `DEBUG` is on, and every error is logged to stderr.

[Unreleased]: https://github.com/christianjbrown/cloud-run-function-lib-php/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/christianjbrown/cloud-run-function-lib-php/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/christianjbrown/cloud-run-function-lib-php/releases/tag/v1.0.0
