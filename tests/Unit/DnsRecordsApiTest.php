<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Tests\Unit;

use ClouDNS\ApiClient\Api\DnsRecordsApi;
use ClouDNS\ApiClient\Enums\RecordType;
use ClouDNS\ApiClient\Enums\RowsPerPage;
use ClouDNS\ApiClient\Enums\Ttl;
use ClouDNS\ApiClient\Http\ClientInterface;
use PHPUnit\Framework\TestCase;

final class DnsRecordsApiTest extends TestCase
{
    public function test_add_record_passes_required_params(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/add-record.json',
                self::callback(function (array $p): bool {
                    return $p['domain-name'] === 'example.com'
                        && $p['type']        === 'A'
                        && $p['host']        === 'www'
                        && $p['record']      === '1.2.3.4'
                        && $p['ttl']         === 3600;
                })
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsRecordsApi($client);
        $api->add('example.com', RecordType::A, 'www', Ttl::OneHour, '1.2.3.4');
    }

    public function test_add_record_with_extra_params(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/add-record.json',
                self::callback(fn(array $p): bool => $p['priority'] === 10)
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsRecordsApi($client);
        $api->add('example.com', RecordType::MX, '@', Ttl::OneHour, 'mail.example.com', ['priority' => 10]);
    }

    public function test_delete_record_passes_record_id(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/dns/delete-record.json', ['domain-name' => 'example.com', 'record-id' => 123])
            ->willReturn(['status' => 'Success']);

        $api = new DnsRecordsApi($client);
        $api->delete('example.com', 123);
    }

    public function test_list_records_passes_optional_filters(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/records.json',
                self::callback(function (array $p): bool {
                    return $p['host'] === 'www'
                        && $p['type'] === 'A'
                        && $p['rows-per-page'] === 20;
                })
            )
            ->willReturn([]);

        $api = new DnsRecordsApi($client);
        $api->list('example.com', 'www', null, RecordType::A, RowsPerPage::Twenty);
    }

    public function test_copy_records_passes_both_domains(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/copy-records.json',
                self::callback(fn(array $p): bool =>
                    $p['domain-name'] === 'source.com' && $p['to-domain'] === 'dest.com'
                )
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsRecordsApi($client);
        $api->copy('source.com', 'dest.com');
    }

    public function test_modify_record_passes_record_id(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/dns/mod-record.json',
                self::callback(fn(array $p): bool => $p['record-id'] === 456)
            )
            ->willReturn(['status' => 'Success']);

        $api = new DnsRecordsApi($client);
        $api->modify('example.com', 456, RecordType::A, 'www', Ttl::OneHour, '5.6.7.8');
    }
}
