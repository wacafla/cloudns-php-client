<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Tests\Unit;

use ClouDNS\ApiClient\Api\AccountApi;
use ClouDNS\ApiClient\Api\DnssecApi;
use ClouDNS\ApiClient\Api\DomainsApi;
use ClouDNS\ApiClient\Api\FailoverApi;
use ClouDNS\ApiClient\Api\MailForwardsApi;
use ClouDNS\ApiClient\Api\MonitoringApi;
use ClouDNS\ApiClient\Api\SlaveZonesApi;
use ClouDNS\ApiClient\Http\ClientInterface;
use PHPUnit\Framework\TestCase;

final class ApiResourcesTest extends TestCase
{
    // -------------------------------------------------------------------------
    // AccountApi
    // -------------------------------------------------------------------------

    public function test_account_login_calls_correct_endpoint(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/login/login.json', [])
            ->willReturn(['status' => 'Success']);

        (new AccountApi($client))->login();
    }

    public function test_account_get_balance_calls_correct_endpoint(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/account/get-balance.json', [])
            ->willReturn(['funds' => '10.00']);

        (new AccountApi($client))->getBalance();
    }

    // -------------------------------------------------------------------------
    // MailForwardsApi
    // -------------------------------------------------------------------------

    public function test_mail_forwards_add_passes_all_params(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/mail-forwards/add.json',
                [
                    'domain-name' => 'example.com',
                    'box'         => 'info',
                    'host'        => 'example.com',
                    'destination' => 'admin@other.com',
                ]
            )
            ->willReturn(['status' => 'Success']);

        (new MailForwardsApi($client))->add('example.com', 'info', 'example.com', 'admin@other.com');
    }

    public function test_mail_forwards_delete_passes_id(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/mail-forwards/delete.json', ['domain-name' => 'example.com', 'mail-forward-id' => 7])
            ->willReturn(['status' => 'Success']);

        (new MailForwardsApi($client))->delete('example.com', 7);
    }

    // -------------------------------------------------------------------------
    // DnssecApi
    // -------------------------------------------------------------------------

    public function test_dnssec_activate_calls_correct_endpoint(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/dns/activate-dnssec.json', ['domain-name' => 'example.com'])
            ->willReturn(['status' => 'Success']);

        (new DnssecApi($client))->activate('example.com');
    }

    public function test_dnssec_get_ds_records_calls_correct_endpoint(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/dns/get-dnssec-ds-records.json', ['domain-name' => 'example.com'])
            ->willReturn([]);

        (new DnssecApi($client))->getDsRecords('example.com');
    }

    // -------------------------------------------------------------------------
    // SlaveZonesApi
    // -------------------------------------------------------------------------

    public function test_slave_zones_add_master_ip(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/dns/add-master-server.json', ['domain-name' => 'example.com', 'master-ip' => '1.2.3.4'])
            ->willReturn(['status' => 'Success']);

        (new SlaveZonesApi($client))->addMasterIp('example.com', '1.2.3.4');
    }

    // -------------------------------------------------------------------------
    // DomainsApi
    // -------------------------------------------------------------------------

    public function test_domains_check_availability(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/domains/check-availability.json', ['domain-name' => 'example.com'])
            ->willReturn(['available' => true]);

        (new DomainsApi($client))->checkAvailability('example.com');
    }

    public function test_domains_modify_name_servers_passes_array(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with(
                '/domains/set-nameservers.json',
                self::callback(fn(array $p): bool =>
                    $p['nameservers'] === ['ns1.example.com', 'ns2.example.com']
                )
            )
            ->willReturn(['status' => 'Success']);

        (new DomainsApi($client))->modifyNameServers('example.com', ['ns1.example.com', 'ns2.example.com']);
    }

    public function test_domains_modify_privacy_protection(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/domains/modify-privacy-protection.json', ['domain-name' => 'example.com', 'status' => 1])
            ->willReturn(['status' => 'Success']);

        (new DomainsApi($client))->modifyPrivacyProtection('example.com', 1);
    }

    // -------------------------------------------------------------------------
    // FailoverApi
    // -------------------------------------------------------------------------

    public function test_failover_activate(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/dns/failover-activate.json', ['domain-name' => 'example.com', 'record-id' => 5])
            ->willReturn(['status' => 'Success']);

        (new FailoverApi($client))->activate('example.com', 5);
    }

    // -------------------------------------------------------------------------
    // MonitoringApi
    // -------------------------------------------------------------------------

    public function test_monitoring_activate(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/monitoring/change-check-status.json', ['check-id' => 3, 'status' => 1])
            ->willReturn(['status' => 'Success']);

        (new MonitoringApi($client))->activate(3);
    }

    public function test_monitoring_deactivate(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects(self::once())
            ->method('post')
            ->with('/monitoring/change-check-status.json', ['check-id' => 3, 'status' => 0])
            ->willReturn(['status' => 'Success']);

        (new MonitoringApi($client))->deactivate(3);
    }
}
