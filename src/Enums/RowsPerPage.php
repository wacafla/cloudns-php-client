<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Enums;

/**
 * Allowed "rows-per-page" values for paginated ClouDNS API endpoints.
 */
enum RowsPerPage: int
{
    case Ten        = 10;
    case Twenty     = 20;
    case Thirty     = 30;
    case Fifty      = 50;
    case OneHundred = 100;
}
