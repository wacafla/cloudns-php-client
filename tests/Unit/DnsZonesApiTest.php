<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Tests\Unit;

use ClouDNS\ApiClient\Api\DnsZonesApi;
use ClouDNS\ApiClient\Enums\RowsPerPage;
use ClouDNS\ApiClient\Enums\ZoneType;
use ClouDNS\ApiClient\Http\ClientInterface;
use PHPUnit\Framework\TestCase;

final class DnsZonesApiTest extends TestCase
{
    private function makeApi(array $returnValue): DnsZonesApi
    {
        $client = $this->createMock(ClientInterface::class);
        $client->method('post')->willReturn($returnValue);

        return new DnsZonesApi($client);
    }

    public function test_register_passes_correct_params(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/register.json',
                self::callback(function (array $params): bool {
                    return $params['domain-name'] === 'example.com'
                        && $params['zone-type'] === 'master';
                })
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsZonesApi($client);
        $api->register('example.com', ZoneType::Master);
    }

    public function test_register_with_ns_passes_ns_array(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/register.json',
                self::callback(function (array $params): bool {
                    return isset($params['ns'])
                        && $params['ns'] === ['ns1.example.com', 'ns2.example.com'];
                })
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsZonesApi($client);
        $api->register('example.com', ZoneType::Master, ['ns1.example.com', 'ns2.example.com']);
    }

    public function test_list_passes_pagination_params(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/list-zones.json',
                self::callback(function (array $params): bool {
                    return $params['page'] === 2
                        && $params['rows-per-page'] === 20;
                })
            )
            ->willReturn([]);

        $api = new DnsZonesApi($client);
        $api->list(2, RowsPerPage::Twenty);
    }

    public function test_delete_passes_domain_name(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/dns/delete.json', ['domain-name' => 'example.com'])
            ->willReturn(['status' => 'Success']);

        $api = new DnsZonesApi($client);
        $api->delete('example.com');
    }

    public function test_get_info_passes_domain_name(): void
    {
        $api = $this->makeApi(['name' => 'example.com', 'type' => 'master']);

        $result = $api->getInfo('example.com');

        self::assertSame('example.com', $result['name']);
    }

    public function test_change_status_passes_status(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/change-status.json',
                self::callback(fn(array $p): bool => $p['status'] === 1)
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsZonesApi($client);
        $api->changeStatus('example.com', 1);
    }
}
