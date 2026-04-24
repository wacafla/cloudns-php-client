<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Enums;

/**
 * DNS zone types supported by the ClouDNS API.
 */
enum ZoneType: string
{
    case Master  = 'master';
    case Slave   = 'slave';
    case Parked  = 'parked';
    case GeoDNS  = 'geodns';
}
