<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Enums\RowsPerPage;
use ClouDNS\ApiClient\Enums\ZoneType;
use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * DNS Zone management API.
 *
 * Covers: register, delete, list, get pages count, get statistics, get zone info,
 * update zone, update status, is-updated, change status, get records statistics,
 * available name servers, DNS plan details.
 *
 * @see https://www.cloudns.net/wiki/article/48/
 */
final class DnsZonesApi
{
    public function __construct(private readonly ClientInterface $client) {}

    // -------------------------------------------------------------------------
    // Zone CRUD
    // -------------------------------------------------------------------------

    /**
     * Register a new DNS zone.
     *
     * @param  string[]|null $ns       Custom NS records (master zones only).
     * @param  string|null   $masterIp Master server IP (slave zones only).
     * @return array<string, mixed>
     */
    public function register(
        string   $domainName,
        ZoneType $zoneType,
        ?array   $ns = null,
        ?string  $masterIp = null,
    ): array {
        $params = [
            'domain-name' => $domainName,
            'zone-type'   => $zoneType->value,
        ];

        if ($ns !== null) {
            $params['ns'] = $ns;
        }

        if ($masterIp !== null) {
            $params['master-ip'] = $masterIp;
        }

        return $this->client->post('/dns/register.json', $params);
    }

    /**
     * Delete a DNS zone.
     *
     * @return array<string, mixed>
     */
    public function delete(string $domainName): array
    {
        return $this->client->post('/dns/delete.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // Zone listing & pagination
    // -------------------------------------------------------------------------

    /**
     * List zones with optional search and group filter.
     *
     * @return array<string, mixed>
     */
    public function list(
        int         $page,
        RowsPerPage $rowsPerPage = RowsPerPage::Ten,
        ?string     $search = null,
        int|string|null $groupId = null,
        ?int        $hasCloudDomains = null,
    ): array {
        $params = [
            'page'          => $page,
            'rows-per-page' => $rowsPerPage->value,
        ];

        if ($search !== null) {
            $params['search'] = $search;
        }

        if ($groupId !== null) {
            $params['group-id'] = $groupId;
        }

        if ($hasCloudDomains !== null) {
            $params['has-cloud-domains'] = $hasCloudDomains;
        }

        return $this->client->post('/dns/list-zones.json', $params);
    }

    /**
     * Get the total number of pages for zone listing.
     *
     * @return array<string, mixed>
     */
    public function getPagesCount(
        RowsPerPage $rowsPerPage = RowsPerPage::Ten,
        ?string     $search = null,
    ): array {
        $params = ['rows-per-page' => $rowsPerPage->value];

        if ($search !== null) {
            $params['search'] = $search;
        }

        return $this->client->post('/dns/get-pages-count.json', $params);
    }

    /**
     * Get zone statistics (total zones, active, inactive, etc.).
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        return $this->client->post('/dns/get-zones-stats.json');
    }

    // -------------------------------------------------------------------------
    // Zone information & state
    // -------------------------------------------------------------------------

    /**
     * Get detailed information about a specific zone.
     *
     * @return array<string, mixed>
     */
    public function getInfo(string $domainName): array
    {
        return $this->client->post('/dns/get-zone-info.json', ['domain-name' => $domainName]);
    }

    /**
     * Trigger a zone update (re-publish to name servers).
     *
     * @return array<string, mixed>
     */
    public function update(string $domainName): array
    {
        return $this->client->post('/dns/update-zone.json', ['domain-name' => $domainName]);
    }

    /**
     * Get the update status of a zone.
     *
     * @return array<string, mixed>
     */
    public function getUpdateStatus(string $domainName): array
    {
        return $this->client->post('/dns/update-status.json', ['domain-name' => $domainName]);
    }

    /**
     * Check whether a zone has been updated on all name servers.
     *
     * @return array<string, mixed>
     */
    public function isUpdated(string $domainName): array
    {
        return $this->client->post('/dns/is-updated.json', ['domain-name' => $domainName]);
    }

    /**
     * Activate or deactivate a zone.
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

    /**
     * Get record statistics for a zone.
     *
     * @return array<string, mixed>
     */
    public function getRecordsStatistics(string $domainName): array
    {
        return $this->client->post('/dns/get-records-stats.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // Name servers & plan
    // -------------------------------------------------------------------------

    /**
     * Get available name servers for the account.
     *
     * @return array<string, mixed>
     */
    public function getAvailableNameServers(): array
    {
        return $this->client->post('/dns/available-name-servers.json');
    }

    /**
     * Get the DNS plan details for the account.
     *
     * @return array<string, mixed>
     */
    public function getPlanDetails(): array
    {
        return $this->client->post('/dns/get-dns-zone-stats.json');
    }

    // -------------------------------------------------------------------------
    // SOA
    // -------------------------------------------------------------------------

    /**
     * Get the SOA record details for a zone.
     *
     * @return array<string, mixed>
     */
    public function getSoaDetails(string $domainName): array
    {
        return $this->client->post('/dns/soa-details.json', ['domain-name' => $domainName]);
    }

    /**
     * Modify the SOA record for a zone.
     *
     * @param  string|null $primaryNs    Primary name server.
     * @param  string|null $adminMail    Admin e-mail address.
     * @param  int|null    $refresh      Refresh interval in seconds.
     * @param  int|null    $retry        Retry interval in seconds.
     * @param  int|null    $expire       Expire interval in seconds.
     * @param  int|null    $defaultTtl   Default TTL in seconds.
     * @return array<string, mixed>
     */
    public function modifySoa(
        string  $domainName,
        ?string $primaryNs  = null,
        ?string $adminMail  = null,
        ?int    $refresh    = null,
        ?int    $retry      = null,
        ?int    $expire     = null,
        ?int    $defaultTtl = null,
    ): array {
        $params = ['domain-name' => $domainName];

        if ($primaryNs !== null)  { $params['primary-ns']  = $primaryNs; }
        if ($adminMail !== null)  { $params['admin-mail']  = $adminMail; }
        if ($refresh !== null)    { $params['refresh']     = $refresh; }
        if ($retry !== null)      { $params['retry']       = $retry; }
        if ($expire !== null)     { $params['expire']      = $expire; }
        if ($defaultTtl !== null) { $params['default-ttl'] = $defaultTtl; }

        return $this->client->post('/dns/modify-soa.json', $params);
    }

    /**
     * Reset the SOA record to default values.
     *
     * @return array<string, mixed>
     */
    public function resetSoa(string $domainName): array
    {
        return $this->client->post('/dns/reset-soa.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // Zone transfer / import
    // -------------------------------------------------------------------------

    /**
     * Import zone records via AXFR transfer from a master server.
     *
     * @return array<string, mixed>
     */
    public function importViaTransfer(string $domainName, string $server): array
    {
        return $this->client->post('/dns/axfr-import.json', [
            'domain-name' => $domainName,
            'server'      => $server,
        ]);
    }

    // -------------------------------------------------------------------------
    // Query statistics
    // -------------------------------------------------------------------------

    /**
     * Get query statistics for a zone.
     *
     * @param  string $period  Allowed: "day", "week", "month", "year"
     * @return array<string, mixed>
     */
    public function getQueryStatistics(string $domainName, string $period = 'month'): array
    {
        return $this->client->post('/dns/get-query-stats.json', [
            'domain-name' => $domainName,
            'period'      => $period,
        ]);
    }
}
