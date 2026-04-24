<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient;

use ClouDNS\ApiClient\Api\AccountApi;
use ClouDNS\ApiClient\Api\DnsZonesApi;
use ClouDNS\ApiClient\Api\DnsRecordsApi;
use ClouDNS\ApiClient\Api\DnssecApi;
use ClouDNS\ApiClient\Api\DomainsApi;
use ClouDNS\ApiClient\Api\FailoverApi;
use ClouDNS\ApiClient\Api\MailForwardsApi;
use ClouDNS\ApiClient\Api\MonitoringApi;
use ClouDNS\ApiClient\Api\SlaveZonesApi;
use ClouDNS\ApiClient\Http\Client;
use ClouDNS\ApiClient\Http\ClientInterface as ClouDNSClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Main entry point for the ClouDNS API client.
 *
 * Usage:
 *
 * ```php
 * $cloudns = ClouDNS::make(
 *     credentials: Credentials::withAuthId(12345, 's3cr3t'),
 *     httpClient:  $psrClient,
 *     requestFactory: $psrRequestFactory,
 *     streamFactory:  $psrStreamFactory,
 * );
 *
 * $zones = $cloudns->zones()->list(page: 1, rowsPerPage: RowsPerPage::Ten);
 * ```
 */
final class ClouDNS
{
    private readonly Client $client;

    public function __construct(
        Credentials             $credentials,
        ClientInterface         $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface  $streamFactory,
    ) {
        $this->client = new Client($credentials, $httpClient, $requestFactory, $streamFactory);
    }

    /**
     * Factory method for convenient construction.
     */
    public static function make(
        Credentials             $credentials,
        ClientInterface         $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface  $streamFactory,
    ): self {
        return new self($credentials, $httpClient, $requestFactory, $streamFactory);
    }

    // -------------------------------------------------------------------------
    // Resource accessors
    // -------------------------------------------------------------------------

    /** Account-level operations (login, IP, balance). */
    public function account(): AccountApi
    {
        return new AccountApi($this->client);
    }

    /** DNS zone management (register, delete, list, update, etc.). */
    public function zones(): DnsZonesApi
    {
        return new DnsZonesApi($this->client);
    }

    /** DNS record management (add, delete, modify, list, etc.). */
    public function records(): DnsRecordsApi
    {
        return new DnsRecordsApi($this->client);
    }

    /** DNSSEC management. */
    public function dnssec(): DnssecApi
    {
        return new DnssecApi($this->client);
    }

    /** Slave zone management. */
    public function slaveZones(): SlaveZonesApi
    {
        return new SlaveZonesApi($this->client);
    }

    /** Mail forwards management. */
    public function mailForwards(): MailForwardsApi
    {
        return new MailForwardsApi($this->client);
    }

    /** Domain name registrar operations. */
    public function domains(): DomainsApi
    {
        return new DomainsApi($this->client);
    }

    /** DNS Failover management. */
    public function failover(): FailoverApi
    {
        return new FailoverApi($this->client);
    }

    /** Monitoring checks management. */
    public function monitoring(): MonitoringApi
    {
        return new MonitoringApi($this->client);
    }
}
