<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * DNSSEC management API.
 *
 * Covers: activate, deactivate, get DS records, get DNSKEY records.
 *
 * @see https://www.cloudns.net/wiki/article/118/
 */
final class DnssecApi
{
    public function __construct(private readonly ClientInterface $client) {}

    /**
     * Activate DNSSEC for a zone.
     *
     * @return array<string, mixed>
     */
    public function activate(string $domainName): array
    {
        return $this->client->post('/dns/activate-dnssec.json', ['domain-name' => $domainName]);
    }

    /**
     * Deactivate DNSSEC for a zone.
     *
     * @return array<string, mixed>
     */
    public function deactivate(string $domainName): array
    {
        return $this->client->post('/dns/deactivate-dnssec.json', ['domain-name' => $domainName]);
    }

    /**
     * Get the DS records for a DNSSEC-enabled zone.
     *
     * @return array<string, mixed>
     */
    public function getDsRecords(string $domainName): array
    {
        return $this->client->post('/dns/get-dnssec-ds-records.json', ['domain-name' => $domainName]);
    }

    /**
     * Get the DNSKEY records for a DNSSEC-enabled zone.
     *
     * @return array<string, mixed>
     */
    public function getDnskeyRecords(string $domainName): array
    {
        return $this->client->post('/dns/get-dnssec-dnskey-records.json', ['domain-name' => $domainName]);
    }
}
