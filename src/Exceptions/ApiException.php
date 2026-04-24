<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Exceptions;

/**
 * Thrown when the ClouDNS API returns a "Failed" status in its response body.
 */
class ApiException extends ClouDNSException
{
    public function __construct(
        public readonly string $statusDescription,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            message: sprintf('ClouDNS API error: %s', $statusDescription),
            code: $code,
            previous: $previous,
        );
    }
}
