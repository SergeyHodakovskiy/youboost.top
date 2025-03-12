<?php

namespace App\Application\Security;

enum Provider: string
{
    case EMAIL_PASSWORD = 'email_password';
    case GOOGLE = 'google';

    public static function isValid(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    public static function fromString(string $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        throw new \InvalidArgumentException("Invalid provider: $value");
    }
}
