<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * DNS Failover management API.
 *
 * Covers: activate, deactivate, get failover info, list checks,
 * add/modify/delete checks.
 *
 * @see https://www.cloudns.net/wiki/article/73/
 */
final class FailoverApi
{
    public function __construct(private readonly ClientInterface $client) {}

    /**
     * Activate DNS Failover for a record.
     *
     * @return array<string, mixed>
     */
    public function activate(string $domainName, int $recordId): array
    {
        return $this->client->post('/dns/failover-activate.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ]);
    }

    /**
     * Deactivate DNS Failover for a record.
     *
     * @return array<string, mixed>
     */
    public function deactivate(string $domainName, int $recordId): array
    {
        return $this->client->post('/dns/failover-deactivate.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ]);
    }

    /**
     * Get the failover configuration for a record.
     *
     * @return array<string, mixed>
     */
    public function getInfo(string $domainName, int $recordId): array
    {
        return $this->client->post('/dns/failover-info.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ]);
    }

    /**
     * Modify the failover configuration for a record.
     *
     * @param  array<string, mixed> $params Failover-specific parameters.
     * @return array<string, mixed>
     */
    public function modify(string $domainName, int $recordId, array $params = []): array
    {
        return $this->client->post('/dns/failover-modify.json', array_merge([
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ], $params));
    }
}
