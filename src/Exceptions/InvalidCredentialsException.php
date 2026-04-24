<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Exceptions;

/**
 * Thrown when the provided credentials are structurally invalid (e.g. no identifier given).
 */
class InvalidCredentialsException extends ClouDNSException {}
