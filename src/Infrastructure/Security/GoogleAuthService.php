<?php

namespace App\Infrastructure\Security;

use Google_Client;

class GoogleAuthService
{
    private Google_Client $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setClientId('YOUR_GOOGLE_CLIENT_ID');
        $this->client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
        $this->client->setRedirectUri('http://localhost:8000/register'); // Укажи свой URL
    }

    public function verifyIdToken(string $idToken): ?array
    {
        $payload = $this->client->verifyIdToken($idToken);
        if (!$payload) {
            return null;
        }
        return $payload; // ['email', 'sub', ...]
    }
}
