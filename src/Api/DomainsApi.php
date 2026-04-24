<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Enums\RowsPerPage;
use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * Domain Names registrar API.
 *
 * Covers: check availability, price list, register, renew, transfer,
 * list, get info, contacts (get/modify), name servers (get/modify),
 * child name servers (get/add/delete/modify), privacy protection,
 * transfer lock, transfer code, RAA status, contact countries.
 *
 * @see https://www.cloudns.net/wiki/article/96/
 */
final class DomainsApi
{
    public function __construct(private readonly ClientInterface $client) {}

    // -------------------------------------------------------------------------
    // Availability & pricing
    // -------------------------------------------------------------------------

    /**
     * Check domain name availability.
     *
     * @return array<string, mixed>
     */
    public function checkAvailability(string $domainName): array
    {
        return $this->client->post('/domains/check-availability.json', [
            'domain-name' => $domainName,
        ]);
    }

    /**
     * Get the domain price list.
     *
     * @return array<string, mixed>
     */
    public function getPriceList(): array
    {
        return $this->client->post('/domains/price-list.json');
    }

    // -------------------------------------------------------------------------
    // Registration lifecycle
    // -------------------------------------------------------------------------

    /**
     * Register a new domain name.
     *
     * @param  array<string, mixed> $contacts  Associative array of contact fields.
     * @param  string[]             $nameservers Array of name server hostnames.
     * @param  int                  $period    Registration period in years.
     * @param  array<string, mixed> $extra     Additional TLD-specific parameters.
     * @return array<string, mixed>
     */
    public function register(
        string $domainName,
        array  $contacts,
        array  $nameservers,
        int    $period = 1,
        array  $extra = [],
    ): array {
        $params = array_merge([
            'domain-name' => $domainName,
            'period'      => $period,
            'nameservers' => $nameservers,
        ], $contacts, $extra);

        return $this->client->post('/domains/register.json', $params);
    }

    /**
     * Renew a domain registration.
     *
     * @return array<string, mixed>
     */
    public function renew(string $domainName, int $period = 1): array
    {
        return $this->client->post('/domains/renew.json', [
            'domain-name' => $domainName,
            'period'      => $period,
        ]);
    }

    /**
     * Initiate a domain transfer in.
     *
     * @param  array<string, mixed> $contacts Contact fields.
     * @return array<string, mixed>
     */
    public function transfer(string $domainName, string $transferCode, array $contacts = []): array
    {
        $params = array_merge([
            'domain-name'   => $domainName,
            'transfer-code' => $transferCode,
        ], $contacts);

        return $this->client->post('/domains/transfer.json', $params);
    }

    // -------------------------------------------------------------------------
    // Listing
    // -------------------------------------------------------------------------

    /**
     * List registered domains with optional search.
     *
     * @return array<string, mixed>
     */
    public function list(
        int         $page,
        RowsPerPage $rowsPerPage = RowsPerPage::Ten,
        ?string     $search = null,
    ): array {
        $params = [
            'page'          => $page,
            'rows-per-page' => $rowsPerPage->value,
        ];

        if ($search !== null) {
            $params['search'] = $search;
        }

        return $this->client->post('/domains/list.json', $params);
    }

    /**
     * Get the number of pages for domain listing.
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

        return $this->client->post('/domains/get-pages-count.json', $params);
    }

    // -------------------------------------------------------------------------
    // Domain information
    // -------------------------------------------------------------------------

    /**
     * Get detailed information about a registered domain.
     *
     * @return array<string, mixed>
     */
    public function getInfo(string $domainName): array
    {
        return $this->client->post('/domains/get-info.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // Contacts
    // -------------------------------------------------------------------------

    /**
     * Get the WHOIS contacts for a domain.
     *
     * @return array<string, mixed>
     */
    public function getContacts(string $domainName): array
    {
        return $this->client->post('/domains/get-contacts.json', ['domain-name' => $domainName]);
    }

    /**
     * Modify the WHOIS contacts for a domain.
     *
     * @param  array<string, mixed> $contacts Contact fields.
     * @return array<string, mixed>
     */
    public function modifyContacts(string $domainName, array $contacts): array
    {
        return $this->client->post('/domains/modify-contacts.json', array_merge(
            ['domain-name' => $domainName],
            $contacts,
        ));
    }

    /**
     * Get available contact countries.
     *
     * @return array<string, mixed>
     */
    public function getAvailableContactCountries(): array
    {
        return $this->client->post('/domains/get-available-contact-countries.json');
    }

    // -------------------------------------------------------------------------
    // Name servers
    // -------------------------------------------------------------------------

    /**
     * Get the name servers for a domain.
     *
     * @return array<string, mixed>
     */
    public function getNameServers(string $domainName): array
    {
        return $this->client->post('/domains/get-nameservers.json', ['domain-name' => $domainName]);
    }

    /**
     * Modify the name servers for a domain.
     *
     * @param  string[] $nameservers
     * @return array<string, mixed>
     */
    public function modifyNameServers(string $domainName, array $nameservers): array
    {
        return $this->client->post('/domains/set-nameservers.json', [
            'domain-name' => $domainName,
            'nameservers' => $nameservers,
        ]);
    }

    // -------------------------------------------------------------------------
    // Child name servers (glue records)
    // -------------------------------------------------------------------------

    /**
     * Get child name servers (glue records) for a domain.
     *
     * @return array<string, mixed>
     */
    public function getChildNameServers(string $domainName): array
    {
        return $this->client->post('/domains/get-child-nameservers.json', [
            'domain-name' => $domainName,
        ]);
    }

    /**
     * Add a child name server (glue record).
     *
     * @return array<string, mixed>
     */
    public function addChildNameServer(string $domainName, string $host, string $ip): array
    {
        return $this->client->post('/domains/add-child-nameserver.json', [
            'domain-name' => $domainName,
            'host'        => $host,
            'ip'          => $ip,
        ]);
    }

    /**
     * Delete a child name server (glue record).
     *
     * @return array<string, mixed>
     */
    public function deleteChildNameServer(string $domainName, string $host): array
    {
        return $this->client->post('/domains/delete-child-nameserver.json', [
            'domain-name' => $domainName,
            'host'        => $host,
        ]);
    }

    /**
     * Modify a child name server (glue record).
     *
     * @return array<string, mixed>
     */
    public function modifyChildNameServer(string $domainName, string $host, string $oldIp, string $newIp): array
    {
        return $this->client->post('/domains/modify-child-nameserver.json', [
            'domain-name' => $domainName,
            'host'        => $host,
            'old-ip'      => $oldIp,
            'new-ip'      => $newIp,
        ]);
    }

    // -------------------------------------------------------------------------
    // Privacy & transfer
    // -------------------------------------------------------------------------

    /**
     * Modify the WHOIS privacy protection setting.
     *
     * @param  int $status 0 = disabled, 1 = enabled
     * @return array<string, mixed>
     */
    public function modifyPrivacyProtection(string $domainName, int $status): array
    {
        return $this->client->post('/domains/modify-privacy-protection.json', [
            'domain-name' => $domainName,
            'status'      => $status,
        ]);
    }

    /**
     * Modify the transfer lock setting.
     *
     * @param  int $status 0 = unlocked, 1 = locked
     * @return array<string, mixed>
     */
    public function modifyTransferLock(string $domainName, int $status): array
    {
        return $this->client->post('/domains/modify-transfer-lock.json', [
            'domain-name' => $domainName,
            'status'      => $status,
        ]);
    }

    /**
     * Get the EPP/transfer code for a domain.
     *
     * @return array<string, mixed>
     */
    public function getTransferCode(string $domainName): array
    {
        return $this->client->post('/domains/get-transfer-code.json', ['domain-name' => $domainName]);
    }

    // -------------------------------------------------------------------------
    // RAA (Registrant Address Accuracy)
    // -------------------------------------------------------------------------

    /**
     * Get the RAA verification status for a domain.
     *
     * @return array<string, mixed>
     */
    public function getRaaStatus(string $domainName): array
    {
        return $this->client->post('/domains/get-raa-status.json', ['domain-name' => $domainName]);
    }

    /**
     * Resend the RAA verification e-mail for a domain.
     *
     * @return array<string, mixed>
     */
    public function resendRaaVerification(string $domainName): array
    {
        return $this->client->post('/domains/resend-raa-verification.json', [
            'domain-name' => $domainName,
        ]);
    }
}
