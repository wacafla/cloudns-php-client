<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Http;

use ClouDNS\ApiClient\Credentials;
use ClouDNS\ApiClient\Exceptions\ApiException;
use ClouDNS\ApiClient\Exceptions\HttpException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Thin PSR-18 wrapper that handles authentication injection, request building,
 * and response decoding for the ClouDNS JSON API.
 */
final class Client implements ClientInterface
{
    // Note: ClientInterface here refers to ClouDNS\ApiClient\Http\ClientInterface
    private const BASE_URI = 'https://api.cloudns.net';

    public function __construct(
        private readonly Credentials             $credentials,
        private readonly PsrClientInterface       $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface  $streamFactory,
    ) {}

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
    public function post(string $path, array $params = []): array
    {
        $allParams = array_merge($this->credentials->toParams(), $params);
        $body      = $this->buildFormBody($allParams);

        $request = $this->requestFactory
            ->createRequest('POST', self::BASE_URI . $path)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Accept', 'application/json')
            ->withBody($this->streamFactory->createStream($body));

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new HttpException(
                message: sprintf('HTTP request to "%s" failed: %s', $path, $e->getMessage()),
                previous: $e,
            );
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new HttpException(
                sprintf('Unexpected HTTP status %d for "%s".', $statusCode, $path),
            );
        }

        $raw = (string) $response->getBody();

        /** @var mixed $decoded */
        $decoded = json_decode($raw, associative: true, flags: JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new HttpException(sprintf('Unexpected non-array JSON response from "%s".', $path));
        }

        // The ClouDNS API signals errors via a top-level "status" key set to "Failed".
        if (isset($decoded['status']) && $decoded['status'] === 'Failed') {
            throw new ApiException(
                statusDescription: (string) ($decoded['statusDescription'] ?? 'Unknown error'),
            );
        }

        return $decoded;
    }

    /**
     * Build a URL-encoded form body, handling array parameters (e.g. ns[]=value).
     *
     * @param array<string, mixed> $params
     */
    private function buildFormBody(array $params): string
    {
        $parts = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $parts[] = urlencode($key . '[]') . '=' . urlencode((string) $item);
                }
            } elseif ($value !== null) {
                $parts[] = urlencode($key) . '=' . urlencode((string) $value);
            }
        }

        return implode('&', $parts);
    }
}
