<?php

declare(strict_types=1);

namespace App\Domain\User\Service;


use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use BadMethodCallException;

class PreferenceService
{

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}


    public function setPreference(User $user, string $key, mixed $value): void
    {
        $preferences = $user->getPreferences();
        $preferences[$key] = $value;

        $user->setPreferences($preferences);
        $this->userRepository->save($user);
    }

    public function hasPreference(User $user, string $key): bool
    {
        return array_key_exists($key, $user->getPreferences());
    }

    public function resetPreferences(User $user): void
    {
        $user->setPreferences([]);
        $this->userRepository->save($user);
    }

    public function updatePreferences(User $user, array $newPreferences): void
    {
        $user->setPreferences(array_merge($user->getPreferences(), $newPreferences));
        $this->userRepository->save($user);
    }

    public function __call(string $method, array $arguments)
    {
        if (preg_match('/^(get|set|has)Preference(.+)$/', $method, $matches)) {
            $action = $matches[1];
            $key = lcfirst($matches[2]);
            return match ($action) {
                'get' => $this->getPreference($arguments[0], $key, $arguments[1] ?? null),
                'set' => $this->setPreference($arguments[0], $key, $arguments[1]),
                'has' => $this->hasPreference($arguments[0], $key),
                default => throw new BadMethodCallException(sprintf('Method %s::%s does not exist', static::class, $method))
            };
        }

        throw new BadMethodCallException(sprintf('Method %s::%s does not exist', static::class, $method));
    }
}
