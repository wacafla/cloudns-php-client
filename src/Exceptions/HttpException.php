<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Exceptions;

/**
 * Thrown when an HTTP transport error occurs (e.g. connection failure, non-2xx response).
 */
class HttpException extends ClouDNSException {}
