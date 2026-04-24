<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Tests\Unit;

use ClouDNS\ApiClient\Credentials;
use ClouDNS\ApiClient\Exceptions\ApiException;
use ClouDNS\ApiClient\Exceptions\HttpException;
use ClouDNS\ApiClient\Http\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface as PsrClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class HttpClientTest extends TestCase
{
    private function makeClient(
        string $responseBody,
        int    $statusCode = 200,
    ): Client {
        $credentials = Credentials::withAuthId(1, 'pass');

        // --- Stream factory ---
        $requestStream = $this->createMock(StreamInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturn($requestStream);

        // --- Request ---
        $request = $this->createMock(RequestInterface::class);
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();

        // --- Request factory ---
        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        // --- Response body stream ---
        $bodyStream = $this->createMock(StreamInterface::class);
        $bodyStream->method('__toString')->willReturn($responseBody);

        // --- Response ---
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getBody')->willReturn($bodyStream);

        // --- HTTP client ---
        $httpClient = $this->createMock(PsrClientInterface::class);
        $httpClient->method('sendRequest')->willReturn($response);

        return new Client($credentials, $httpClient, $requestFactory, $streamFactory);
    }

    public function test_successful_response_is_decoded(): void
    {
        $client = $this->makeClient('{"status":"Success","statusDescription":"Zone registered."}');

        $result = $client->post('/dns/register.json', ['domain-name' => 'example.com']);

        self::assertSame('Success', $result['status']);
        self::assertSame('Zone registered.', $result['statusDescription']);
    }

    public function test_failed_api_status_throws_api_exception(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('ClouDNS API error: Invalid authentication');

        $client = $this->makeClient('{"status":"Failed","statusDescription":"Invalid authentication"}');
        $client->post('/login/login.json');
    }

    public function test_non_2xx_http_status_throws_http_exception(): void
    {
        $this->expectException(HttpException::class);

        $client = $this->makeClient('', 500);
        $client->post('/dns/register.json');
    }

    public function test_array_params_are_serialised_correctly(): void
    {
        $credentials = Credentials::withAuthId(1, 'pass');

        $capturedBody = '';

        $requestStream = $this->createMock(StreamInterface::class);
        $streamFactory = $this->createMock(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturnCallback(
            function (string $body) use (&$capturedBody, $requestStream): StreamInterface {
                $capturedBody = $body;
                return $requestStream;
            }
        );

        $request = $this->createMock(RequestInterface::class);
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();

        $requestFactory = $this->createMock(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        $bodyStream = $this->createMock(StreamInterface::class);
        $bodyStream->method('__toString')->willReturn('{"status":"Success"}');

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($bodyStream);

        $httpClient = $this->createMock(PsrClientInterface::class);
        $httpClient->method('sendRequest')->willReturn($response);

        $client = new Client($credentials, $httpClient, $requestFactory, $streamFactory);
        $client->post('/dns/register.json', ['ns' => ['ns1.example.com', 'ns2.example.com']]);

        self::assertStringContainsString('ns%5B%5D=ns1.example.com', $capturedBody);
        self::assertStringContainsString('ns%5B%5D=ns2.example.com', $capturedBody);
    }
}
