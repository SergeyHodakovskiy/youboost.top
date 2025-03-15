<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use Google_Client;
use RuntimeException;

class GoogleAuthService
{
    private Google_Client $client;

    public function __construct(string $googleClientId)
    {
        $this->client = new Google_Client(['client_id' => $googleClientId]);
    }

    public function verifyIdToken(string $idToken): ?array
    {
        $payload = $this->client->verifyIdToken($idToken);

        if (!$payload) {
            return null;
        }

        return $payload;
    }
}
