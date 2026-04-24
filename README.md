# ClouDNS PHP API Client

A robust, fully typed, and idiomatic PHP 8.1+ client for the [ClouDNS HTTP API](https://www.cloudns.net/wiki/article/42/). This package provides an elegant object-oriented interface for managing DNS zones, records, domain names, DNSSEC, failover, monitoring, and mail forwards.

## Features

- **Full API Coverage:** Supports DNS zones, DNS records, domain registration, slave zones, mail forwards, failover, monitoring, and DNSSEC.
- **Strong Typing:** Uses PHP 8.1+ features including Enums, strictly typed DTOs, and proper return types.
- **PSR-18 Compatible:** Bring your own HTTP client (Guzzle, Symfony HTTP Client, etc.).
- **Exception Handling:** Distinct exceptions for API-level failures and transport-level errors.

## Requirements

- PHP 8.1 or higher (compatible with PHP 8.4)
- A PSR-18 HTTP Client (e.g., `guzzlehttp/guzzle`)
- PSR-17 HTTP Message Factories (e.g., `guzzlehttp/psr7`)

## Installation

You can install the package via Composer:

```bash
composer require cloudns/api-client
```

Since this library relies on PSR-18 and PSR-17 interfaces, you also need to install implementations for them if you don't have them already. For example, using Guzzle:

```bash
composer require guzzlehttp/guzzle
```

## Quick Start

### 1. Initialize the Client

The ClouDNS API requires authentication. You can authenticate using an API User ID (`auth-id`), a Sub-User ID (`sub-auth-id`), or a Sub-User Username (`sub-auth-user`), along with your password.

```php
use ClouDNS\ApiClient\ClouDNS;
use ClouDNS\ApiClient\Credentials;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;

require 'vendor/autoload.php';

// Create credentials
$credentials = Credentials::withAuthId(12345, 'your-api-password');
// Or for sub-users:
// $credentials = Credentials::withSubAuthId(9876, 'sub-password');
// $credentials = Credentials::withSubAuthUser('my-sub-user', 'sub-password');

// Provide PSR-18 and PSR-17 implementations (using Guzzle here)
$httpClient = new GuzzleClient();
$httpFactory = new HttpFactory();

// Instantiate the ClouDNS client
$cloudns = ClouDNS::make(
    credentials: $credentials,
    httpClient: $httpClient,
    requestFactory: $httpFactory,
    streamFactory: $httpFactory
);
```

### 2. Managing DNS Zones

```php
use ClouDNS\ApiClient\Enums\ZoneType;
use ClouDNS\ApiClient\Enums\RowsPerPage;

// Register a new master zone
$cloudns->zones()->register('example.com', ZoneType::Master);

// List zones (page 1, 20 rows per page)
$zones = $cloudns->zones()->list(page: 1, rowsPerPage: RowsPerPage::Twenty);

// Get zone statistics
$stats = $cloudns->zones()->getStatistics();

// Delete a zone
$cloudns->zones()->delete('example.com');
```

### 3. Managing DNS Records

```php
use ClouDNS\ApiClient\Enums\RecordType;
use ClouDNS\ApiClient\Enums\Ttl;

// Add an A record
$cloudns->records()->add(
    domainName: 'example.com',
    type: RecordType::A,
    host: 'www',
    ttl: Ttl::OneHour,
    record: '192.168.1.100'
);

// Add an MX record (requires extra parameters)
$cloudns->records()->add(
    domainName: 'example.com',
    type: RecordType::MX,
    host: '@',
    ttl: Ttl::OneDay,
    record: 'mail.example.com',
    extra: ['priority' => 10]
);

// List records for a zone
$records = $cloudns->records()->list('example.com');

// Delete a record by ID
$cloudns->records()->delete('example.com', recordId: 456789);
```

### 4. Domain Name Registration

```php
// Check domain availability
$status = $cloudns->domains()->checkAvailability('my-new-startup.com');

if ($status['available']) {
    // Register the domain
    $cloudns->domains()->register(
        domainName: 'my-new-startup.com',
        contacts: [
            'registrant-firstname' => 'John',
            'registrant-lastname'  => 'Doe',
            'registrant-email'     => 'john@example.com',
            // ... other required contact fields
        ],
        nameservers: ['ns1.cloudns.net', 'ns2.cloudns.net'],
        period: 1 // 1 year
    );
}
```

### 5. Error Handling

The client throws specific exceptions when things go wrong:

```php
use ClouDNS\ApiClient\Exceptions\ApiException;
use ClouDNS\ApiClient\Exceptions\HttpException;

try {
    $cloudns->zones()->delete('non-existent.com');
} catch (ApiException $e) {
    // Thrown when the ClouDNS API returns a "Failed" status
    echo "API Error: " . $e->getMessage(); // e.g. "Missing domain-name"
} catch (HttpException $e) {
    // Thrown on network errors or non-2xx HTTP responses
    echo "Network Error: " . $e->getMessage();
}
```

## Available API Resources

The `$cloudns` instance provides access to various API resource groups:

- `$cloudns->account()` - Login tests, IP checks, account balance.
- `$cloudns->zones()` - Master/Slave/Parked/GeoDNS zone management, SOA records, transfers.
- `$cloudns->records()` - DNS record CRUD, BIND export/import.
- `$cloudns->domains()` - Domain registration, renewals, transfers, WHOIS contacts, nameservers.
- `$cloudns->dnssec()` - Activate/deactivate DNSSEC, fetch DS and DNSKEY records.
- `$cloudns->failover()` - DNS Failover configuration.
- `$cloudns->monitoring()` - Uptime monitoring checks.
- `$cloudns->slaveZones()` - Manage master IPs for slave zones.
- `$cloudns->mailForwards()` - Manage email forwarding.

## Testing

The package includes a comprehensive PHPUnit test suite.

```bash
composer test
```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
