# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the package uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/christianjbrown/cloud-run-function-lib-php/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/christianjbrown/cloud-run-function-lib-php/releases/tag/v1.0.0
