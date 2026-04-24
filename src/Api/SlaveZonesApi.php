<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * Slave zone management API.
 *
 * Covers: add master IP, delete master IP, list master IPs,
 * change slave zone status.
 *
 * @see https://www.cloudns.net/wiki/article/63/
 */
final class SlaveZonesApi
{
    public function __construct(private readonly ClientInterface $client) {}

    /**
     * Add a master IP address to a slave zone.
     *
     * @return array<string, mixed>
     */
    public function addMasterIp(string $domainName, string $masterIp): array
    {
        return $this->client->post('/dns/add-master-server.json', [
            'domain-name' => $domainName,
            'master-ip'   => $masterIp,
        ]);
    }

    /**
     * Delete a master IP address from a slave zone.
     *
     * @return array<string, mixed>
     */
    public function deleteMasterIp(string $domainName, string $masterIp): array
    {
        return $this->client->post('/dns/delete-master-server.json', [
            'domain-name' => $domainName,
            'master-ip'   => $masterIp,
        ]);
    }

    /**
     * List all master IP addresses for a slave zone.
     *
     * @return array<string, mixed>
     */
    public function listMasterIps(string $domainName): array
    {
        return $this->client->post('/dns/master-servers.json', ['domain-name' => $domainName]);
    }

    /**
     * Change the status of a slave zone.
     *
     * @param  int $status 0 = inactive, 1 = active
     * @return array<string, mixed>
     */
    public function changeStatus(string $domainName, int $status): array
    {
        return $this->client->post('/dns/change-status.json', [
            'domain-name' => $domainName,
            'status'      => $status,
        ]);
    }
}
