<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Enums;

/**
 * Supported TTL values (in seconds) for DNS records on the ClouDNS platform.
 */
enum Ttl: int
{
    case OneMinute     = 60;
    case FiveMinutes   = 300;
    case FifteenMinutes = 900;
    case ThirtyMinutes = 1800;
    case OneHour       = 3600;
    case SixHours      = 21600;
    case TwelveHours   = 43200;
    case OneDay        = 86400;
    case TwoDays       = 172800;
    case ThreeDays     = 259200;
    case OneWeek       = 604800;
    case TwoWeeks      = 1209600;
    case OneMonth      = 2592000;
}
