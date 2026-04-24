<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Enums\RecordType;
use ClouDNS\ApiClient\Enums\RowsPerPage;
use ClouDNS\ApiClient\Enums\Ttl;
use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * DNS Record management API.
 *
 * Covers: list, get, add, delete, modify, copy, import, export (BIND),
 * get available types, get available TTLs, get records count,
 * get/modify SOA, get dynamic URL, activate/deactivate records.
 *
 * @see https://www.cloudns.net/wiki/article/57/
 * @see https://www.cloudns.net/wiki/article/58/
 */
final class DnsRecordsApi
{
    public function __construct(private readonly ClientInterface $client) {}

    // -------------------------------------------------------------------------
    // Listing
    // -------------------------------------------------------------------------

    /**
     * List DNS records for a zone, with optional filtering and pagination.
     *
     * @return array<string, mixed>
     */
    public function list(
        string      $domainName,
        ?string     $host = null,
        ?string     $hostLike = null,
        ?RecordType $type = null,
        RowsPerPage $rowsPerPage = RowsPerPage::Ten,
        int         $page = 1,
        ?string     $orderBy = null,
    ): array {
        $params = ['domain-name' => $domainName];

        if ($host !== null)     { $params['host']          = $host; }
        if ($hostLike !== null) { $params['host-like']     = $hostLike; }
        if ($type !== null)     { $params['type']          = $type->value; }
        if ($orderBy !== null)  { $params['order-by']      = $orderBy; }

        $params['rows-per-page'] = $rowsPerPage->value;
        $params['page']          = $page;

        return $this->client->post('/dns/records.json', $params);
    }

    /**
     * Get a single record by its ID.
     *
     * @return array<string, mixed>
     */
    public function get(string $domainName, int $recordId): array
    {
        return $this->client->post('/dns/get-record.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ]);
    }

    /**
     * Get the number of pages for record listing.
     *
     * @return array<string, mixed>
     */
    public function getPagesCount(
        string      $domainName,
        RowsPerPage $rowsPerPage = RowsPerPage::Ten,
        ?RecordType $type = null,
        ?string     $host = null,
    ): array {
        $params = [
            'domain-name'   => $domainName,
            'rows-per-page' => $rowsPerPage->value,
        ];

        if ($type !== null) { $params['type'] = $type->value; }
        if ($host !== null) { $params['host'] = $host; }

        return $this->client->post('/dns/get-records-pages-count.json', $params);
    }

    /**
     * Get the total number of records in a zone.
     *
     * @return array<string, mixed>
     */
    public function getCount(string $domainName): array
    {
        return $this->client->post('/dns/get-records-count.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // Create / Update / Delete
    // -------------------------------------------------------------------------

    /**
     * Add a new DNS record.
     *
     * The $extra array accepts type-specific parameters such as:
     *   - priority    (MX, SRV)
     *   - weight      (SRV)
     *   - port        (SRV)
     *   - frame       (WR – web redirect)
     *   - frame-title (WR)
     *   - frame-keywords (WR)
     *   - frame-description (WR)
     *   - save-path   (WR)
     *   - redirect-type (WR)
     *   - mail        (RP)
     *   - txt         (RP)
     *   - algorithm   (SSHFP, DS, TLSA, CERT)
     *   - fptype      (SSHFP)
     *   - key-tag     (DS, TLSA)
     *   - digest-type (DS)
     *   - digest      (DS)
     *   - usage       (TLSA, SMIMEA)
     *   - selector    (TLSA, SMIMEA)
     *   - matching-type (TLSA, SMIMEA)
     *   - type        (CERT)
     *   - key-tag     (CERT)
     *   - caa-flag    (CAA)
     *   - caa-type    (CAA)
     *   - caa-value   (CAA)
     *   - order       (NAPTR)
     *   - pref        (NAPTR)
     *   - flag        (NAPTR)
     *   - params      (NAPTR)
     *   - regexp      (NAPTR)
     *   - replace     (NAPTR)
     *   - cpu         (HINFO)
     *   - os          (HINFO)
     *   - geodns-location (GeoDNS A/AAAA/CNAME/SRV/NAPTR)
     *
     * @param  array<string, mixed> $extra Type-specific parameters.
     * @return array<string, mixed>
     */
    public function add(
        string     $domainName,
        RecordType $type,
        string     $host,
        Ttl        $ttl,
        ?string    $record = null,
        array      $extra = [],
    ): array {
        $params = array_merge([
            'domain-name' => $domainName,
            'type'        => $type->value,
            'host'        => $host,
            'ttl'         => $ttl->value,
        ], $extra);

        if ($record !== null) {
            $params['record'] = $record;
        }

        return $this->client->post('/dns/add-record.json', $params);
    }

    /**
     * Delete a DNS record by its ID.
     *
     * @return array<string, mixed>
     */
    public function delete(string $domainName, int $recordId): array
    {
        return $this->client->post('/dns/delete-record.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ]);
    }

    /**
     * Modify an existing DNS record.
     *
     * @param  array<string, mixed> $extra Type-specific parameters (same as add()).
     * @return array<string, mixed>
     */
    public function modify(
        string     $domainName,
        int        $recordId,
        RecordType $type,
        string     $host,
        Ttl        $ttl,
        ?string    $record = null,
        array      $extra = [],
    ): array {
        $params = array_merge([
            'domain-name' => $domainName,
            'record-id'   => $recordId,
            'type'        => $type->value,
            'host'        => $host,
            'ttl'         => $ttl->value,
        ], $extra);

        if ($record !== null) {
            $params['record'] = $record;
        }

        return $this->client->post('/dns/mod-record.json', $params);
    }

    /**
     * Activate or deactivate a DNS record.
     *
     * @param  int $status 0 = inactive, 1 = active
     * @return array<string, mixed>
     */
    public function changeStatus(string $domainName, int $recordId, int $status): array
    {
        return $this->client->post('/dns/change-record-status.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
            'status'      => $status,
        ]);
    }

    // -------------------------------------------------------------------------
    // Bulk operations
    // -------------------------------------------------------------------------

    /**
     * Copy all records from one zone to another.
     *
     * @param  int $deleteCurrentRecords 0 = keep existing, 1 = delete before copy
     * @return array<string, mixed>
     */
    public function copy(
        string $fromDomain,
        string $toDomain,
        int    $deleteCurrentRecords = 0,
    ): array {
        return $this->client->post('/dns/copy-records.json', [
            'domain-name'            => $fromDomain,
            'to-domain'              => $toDomain,
            'delete-current-records' => $deleteCurrentRecords,
        ]);
    }

    /**
     * Import records into a zone from a BIND-format zone file string.
     *
     * @return array<string, mixed>
     */
    public function import(string $domainName, string $format, string $content): array
    {
        return $this->client->post('/dns/records-import.json', [
            'domain-name' => $domainName,
            'format'      => $format,
            'content'     => $content,
        ]);
    }

    /**
     * Export all records of a zone in BIND format.
     *
     * @return array<string, mixed>
     */
    public function exportBind(string $domainName): array
    {
        return $this->client->post('/dns/records-export.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    /**
     * Get the list of available record types for a zone.
     *
     * @return array<string, mixed>
     */
    public function getAvailableTypes(string $domainName): array
    {
        return $this->client->post('/dns/get-available-record-types.json', [
            'zone-type' => $domainName,
        ]);
    }

    /**
     * Get the list of available TTL values.
     *
     * @return array<string, mixed>
     */
    public function getAvailableTtls(): array
    {
        return $this->client->post('/dns/get-available-ttl.json');
    }

    // -------------------------------------------------------------------------
    // Dynamic DNS
    // -------------------------------------------------------------------------

    /**
     * Get the Dynamic DNS update URL for a record.
     *
     * @return array<string, mixed>
     */
    public function getDynamicUrl(string $domainName, int $recordId): array
    {
        return $this->client->post('/dns/get-dynamic-url.json', [
            'domain-name' => $domainName,
            'record-id'   => $recordId,
        ]);
    }
}
