<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient\Tests\Unit;

use ClouDNS\ApiClient\Credentials;
use PHPUnit\Framework\TestCase;

final class CredentialsTest extends TestCase
{
    public function test_with_auth_id_produces_correct_params(): void
    {
        $creds = Credentials::withAuthId(42, 'secret');

        $params = $creds->toParams();

        self::assertSame(42, $params['auth-id']);
        self::assertSame('secret', $params['auth-password']);
        self::assertArrayNotHasKey('sub-auth-id', $params);
        self::assertArrayNotHasKey('sub-auth-user', $params);
    }

    public function test_with_sub_auth_id_produces_correct_params(): void
    {
        $creds = Credentials::withSubAuthId(99, 'pass');

        $params = $creds->toParams();

        self::assertSame(99, $params['sub-auth-id']);
        self::assertSame('pass', $params['auth-password']);
        self::assertArrayNotHasKey('auth-id', $params);
        self::assertArrayNotHasKey('sub-auth-user', $params);
    }

    public function test_with_sub_auth_user_produces_correct_params(): void
    {
        $creds = Credentials::withSubAuthUser('myuser', 'mypass');

        $params = $creds->toParams();

        self::assertSame('myuser', $params['sub-auth-user']);
        self::assertSame('mypass', $params['auth-password']);
        self::assertArrayNotHasKey('auth-id', $params);
        self::assertArrayNotHasKey('sub-auth-id', $params);
    }

    public function test_credentials_are_readonly(): void
    {
        $creds = Credentials::withAuthId(1, 'pw');

        self::assertSame(1, $creds->authId);
        self::assertNull($creds->subAuthId);
        self::assertNull($creds->subAuthUser);
        self::assertSame('pw', $creds->authPassword);
    }
}
