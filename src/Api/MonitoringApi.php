<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * Monitoring checks management API.
 *
 * Covers: list, add, modify, delete, get info, activate/deactivate checks.
 *
 * @see https://www.cloudns.net/wiki/article/86/
 */
final class MonitoringApi
{
    public function __construct(private readonly ClientInterface $client) {}

    /**
     * List all monitoring checks for the account.
     *
     * @return array<string, mixed>
     */
    public function list(): array
    {
        return $this->client->post('/monitoring/list-checks.json');
    }

    /**
     * Get detailed information about a monitoring check.
     *
     * @return array<string, mixed>
     */
    public function getInfo(int $checkId): array
    {
        return $this->client->post('/monitoring/check-info.json', ['check-id' => $checkId]);
    }

    /**
     * Add a new monitoring check.
     *
     * @param  array<string, mixed> $params Check-specific parameters.
     * @return array<string, mixed>
     */
    public function add(
        string $name,
        string $checkType,
        string $host,
        int    $checkPeriod,
        array  $params = [],
    ): array {
        return $this->client->post('/monitoring/add-check.json', array_merge([
            'name'         => $name,
            'check-type'   => $checkType,
            'host'         => $host,
            'check-period' => $checkPeriod,
        ], $params));
    }

    /**
     * Modify an existing monitoring check.
     *
     * @param  array<string, mixed> $params Fields to update.
     * @return array<string, mixed>
     */
    public function modify(int $checkId, array $params): array
    {
        return $this->client->post('/monitoring/modify-check.json', array_merge(
            ['check-id' => $checkId],
            $params,
        ));
    }

    /**
     * Delete a monitoring check.
     *
     * @return array<string, mixed>
     */
    public function delete(int $checkId): array
    {
        return $this->client->post('/monitoring/delete-check.json', ['check-id' => $checkId]);
    }

    /**
     * Activate a monitoring check.
     *
     * @return array<string, mixed>
     */
    public function activate(int $checkId): array
    {
        return $this->client->post('/monitoring/change-check-status.json', [
            'check-id' => $checkId,
            'status'   => 1,
        ]);
    }

    /**
     * Deactivate a monitoring check.
     *
     * @return array<string, mixed>
     */
    public function deactivate(int $checkId): array
    {
        return $this->client->post('/monitoring/change-check-status.json', [
            'check-id' => $checkId,
            'status'   => 0,
        ]);
    }
}
