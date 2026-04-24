<?php

declare(strict_types=1);

namespace ClouDNS\ApiClient;

/**
 * Represents the authentication credentials for the ClouDNS API.
 *
 * ClouDNS supports three mutually exclusive authentication identifiers:
 *   - auth-id       : A numeric API user ID
 *   - sub-auth-id   : A numeric API sub-user ID
 *   - sub-auth-user : A string username for an API sub-user
 *
 * Exactly one identifier must be provided alongside the auth-password.
 */
final class Credentials
{
    private function __construct(
        public readonly ?int    $authId,
        public readonly ?int    $subAuthId,
        public readonly ?string $subAuthUser,
        public readonly string  $authPassword,
    ) {}

    /**
     * Create credentials using a primary API user ID.
     */
    public static function withAuthId(int $authId, string $authPassword): self
    {
        return new self(
            authId: $authId,
            subAuthId: null,
            subAuthUser: null,
            authPassword: $authPassword,
        );
    }

    /**
     * Create credentials using a sub-user numeric ID.
     */
    public static function withSubAuthId(int $subAuthId, string $authPassword): self
    {
        return new self(
            authId: null,
            subAuthId: $subAuthId,
            subAuthUser: null,
            authPassword: $authPassword,
        );
    }

    /**
     * Create credentials using a sub-user string username.
     */
    public static function withSubAuthUser(string $subAuthUser, string $authPassword): self
    {
        return new self(
            authId: null,
            subAuthId: null,
            subAuthUser: $subAuthUser,
            authPassword: $authPassword,
        );
    }

    /**
     * Returns the credentials as a key-value array suitable for HTTP POST parameters.
     *
     * @return array<string, int|string>
     */
    public function toParams(): array
    {
        $params = ['auth-password' => $this->authPassword];

        if ($this->authId !== null) {
            $params['auth-id'] = $this->authId;
        } elseif ($this->subAuthId !== null) {
            $params['sub-auth-id'] = $this->subAuthId;
        } elseif ($this->subAuthUser !== null) {
            $params['sub-auth-user'] = $this->subAuthUser;
        }

        return $params;
    }
}
