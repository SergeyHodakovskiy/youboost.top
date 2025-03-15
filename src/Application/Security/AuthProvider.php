<?php

declare(strict_types=1);

namespace App\Application\Security;

enum AuthProvider: string
{
    case EMAIL_PASSWORD = 'email-password';
    case GOOGLE = 'google';
    case APPLE = 'apple';
    case TELEGRAM = 'telegram';


    public static function getAvailableProviders(): array
    {
        return array_map(fn(AuthProvider $provider) => $provider->value, self::cases());
    }


    public static function fromString(string $providerName): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $providerName) {
                return $case;
            }
        }

        throw new \InvalidArgumentException('Unsupported provider: ' . $providerName);
    }
}
