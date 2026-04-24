<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Tests\Unit;

use ClouDNS\ApiClient\Enums\RecordType;
use ClouDNS\ApiClient\Enums\RowsPerPage;
use ClouDNS\ApiClient\Enums\Ttl;
use ClouDNS\ApiClient\Enums\ZoneType;
use PHPUnit\Framework\TestCase;

final class EnumsTest extends TestCase
{
    public function test_record_type_values(): void
    {
        self::assertSame('A',          RecordType::A->value);
        self::assertSame('AAAA',       RecordType::AAAA->value);
        self::assertSame('MX',         RecordType::MX->value);
        self::assertSame('CNAME',      RecordType::CNAME->value);
        self::assertSame('TXT',        RecordType::TXT->value);
        self::assertSame('SRV',        RecordType::SRV->value);
        self::assertSame('CAA',        RecordType::CAA->value);
        self::assertSame('TLSA',       RecordType::TLSA->value);
        self::assertSame('PTR',        RecordType::PTR->value);
        self::assertSame('NAPTR',      RecordType::NAPTR->value);
        self::assertSame('OPENPGPKEY', RecordType::OPENPGPKEY->value);
    }

    public function test_zone_type_values(): void
    {
        self::assertSame('master',  ZoneType::Master->value);
        self::assertSame('slave',   ZoneType::Slave->value);
        self::assertSame('parked',  ZoneType::Parked->value);
        self::assertSame('geodns',  ZoneType::GeoDNS->value);
    }

    public function test_ttl_values(): void
    {
        self::assertSame(60,      Ttl::OneMinute->value);
        self::assertSame(3600,    Ttl::OneHour->value);
        self::assertSame(86400,   Ttl::OneDay->value);
        self::assertSame(604800,  Ttl::OneWeek->value);
        self::assertSame(2592000, Ttl::OneMonth->value);
    }

    public function test_rows_per_page_values(): void
    {
        self::assertSame(10,  RowsPerPage::Ten->value);
        self::assertSame(20,  RowsPerPage::Twenty->value);
        self::assertSame(50,  RowsPerPage::Fifty->value);
        self::assertSame(100, RowsPerPage::OneHundred->value);
    }

    public function test_record_type_can_be_created_from_string(): void
    {
        self::assertSame(RecordType::A,    RecordType::from('A'));
        self::assertSame(RecordType::AAAA, RecordType::from('AAAA'));
        self::assertSame(RecordType::MX,   RecordType::from('MX'));
    }

    public function test_ttl_can_be_created_from_int(): void
    {
        self::assertSame(Ttl::OneHour, Ttl::from(3600));
        self::assertSame(Ttl::OneDay,  Ttl::from(86400));
    }
}
