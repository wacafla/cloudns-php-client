<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * Mail Forwards management API.
 *
 * Covers: list, add, delete, modify mail forwards.
 *
 * @see https://www.cloudns.net/wiki/article/82/
 */
final class MailForwardsApi
{
    public function __construct(private readonly ClientInterface $client) {}

    /**
     * List all mail forwards for a domain.
     *
     * @return array<string, mixed>
     */
    public function list(string $domainName): array
    {
        return $this->client->post('/mail-forwards/list.json', ['domain-name' => $domainName]);
    }

    /**
     * Add a new mail forward.
     *
     * @return array<string, mixed>
     */
    public function add(string $domainName, string $box, string $host, string $destination): array
    {
        return $this->client->post('/mail-forwards/add.json', [
            'domain-name' => $domainName,
            'box'         => $box,
            'host'        => $host,
            'destination' => $destination,
        ]);
    }

    /**
     * Delete a mail forward by its ID.
     *
     * @return array<string, mixed>
     */
    public function delete(string $domainName, int $mailForwardId): array
    {
        return $this->client->post('/mail-forwards/delete.json', [
            'domain-name'     => $domainName,
            'mail-forward-id' => $mailForwardId,
        ]);
    }

    /**
     * Modify an existing mail forward.
     *
     * @return array<string, mixed>
     */
    public function modify(
        string $domainName,
        int    $mailForwardId,
        string $box,
        string $host,
        string $destination,
    ): array {
        return $this->client->post('/mail-forwards/modify.json', [
            'domain-name'     => $domainName,
            'mail-forward-id' => $mailForwardId,
            'box'             => $box,
            'host'            => $host,
            'destination'     => $destination,
        ]);
    }

    /**
     * Get the number of available mail forwards for the account.
     *
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        return $this->client->post('/mail-forwards/get-stats.json');
    }
}
