<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Api;

use ClouDNS\ApiClient\Http\ClientInterface;

/**
 * Account-level API methods: login test, current IP, and account balance.
 *
 * @see https://www.cloudns.net/wiki/article/45/
 * @see https://www.cloudns.net/wiki/article/46/
 * @see https://www.cloudns.net/wiki/article/47/
 */
final class AccountApi
{
    public function __construct(private readonly ClientInterface $client) {}

    /**
     * Test that the provided credentials are valid.
     *
     * @return array{status: string, statusDescription: string}
     */
    public function login(): array
    {
        /** @var array{status: string, statusDescription: string} */
        return $this->client->post('/login/login.json');
    }

    /**
     * Get the current IP address of the API server as seen by ClouDNS.
     *
     * @return array{ip: string}
     */
    public function getCurrentIp(): array
    {
        /** @var array{ip: string} */
        return $this->client->post('/ip/get-my-ip.json');
    }

    /**
     * Get the current account balance.
     *
     * @return array{funds: string}
     */
    public function getBalance(): array
    {
        /** @var array{funds: string} */
        return $this->client->post('/account/get-balance.json');
    }
}
