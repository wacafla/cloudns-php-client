<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Enums;

/**
 * All DNS record types supported by the ClouDNS API.
 */
enum RecordType: string
{
    case A           = 'A';
    case AAAA        = 'AAAA';
    case MX          = 'MX';
    case CNAME       = 'CNAME';
    case TXT         = 'TXT';
    case SPF         = 'SPF';
    case NS          = 'NS';
    case SRV         = 'SRV';
    case WR          = 'WR';   // Web Redirect
    case RP          = 'RP';
    case SSHFP       = 'SSHFP';
    case ALIAS       = 'ALIAS';
    case CAA         = 'CAA';
    case TLSA        = 'TLSA';
    case CERT        = 'CERT';
    case DS          = 'DS';
    case PTR         = 'PTR';
    case NAPTR       = 'NAPTR';
    case HINFO       = 'HINFO';
    case LOC         = 'LOC';
    case DNAME       = 'DNAME';
    case SMIMEA      = 'SMIMEA';
    case OPENPGPKEY  = 'OPENPGPKEY';
}
