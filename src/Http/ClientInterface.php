<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Http;

use ClouDNS\ApiClient\Exceptions\ApiException;
use ClouDNS\ApiClient\Exceptions\HttpException;

/**
 * Contract for the ClouDNS HTTP transport layer.
 */
interface ClientInterface
{
    /**
     * Perform a POST request to the given API path and return the decoded response.
     *
     * @param  string               $path   e.g. "/dns/list-zones.json"
     * @param  array<string, mixed> $params Additional POST parameters (auth is injected automatically)
     * @return array<string, mixed>|list<mixed>
     *
     * @throws ApiException  When the API returns a "Failed" status.
     * @throws HttpException When the HTTP transport fails.
     */
    public function post(string $path, array $params = []): array;
}
