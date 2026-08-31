<?php

declare(strict_types=1);

namespace ChristianBrown\CloudRunFunction;

use ChristianBrown\UserFriendlyException\UserFriendlyExceptionInterface;

/**
 * Marks a UserFriendlyException whose cause is the request, not the service.
 *
 * A plain UserFriendlyException answers 500, which is right when the service
 * could not do its job. A malformed path or an unusable parameter is the
 * caller's mistake: the service is healthy and a retry with the same input
 * will fail identically, so it answers 400 instead.
 *
 * The distinction matters beyond correctness. A 500 counts as a service error
 * in Cloud Monitoring, so a bot probing unknown paths raises alerts about a
 * service that is working perfectly.
 */
interface BadRequestExceptionInterface extends UserFriendlyExceptionInterface
{
}
