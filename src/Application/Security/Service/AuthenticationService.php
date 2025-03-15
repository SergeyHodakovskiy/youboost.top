<?php

declare(strict_types=1);

namespace App\Application\Security\Service;

use App\Application\Security\Strategy\Authentication\AuthenticationStrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\User\Entity\User;
use RuntimeException;
use App\Application\Security\AuthProvider;

class AuthenticationService
{
    /** @param iterable<AuthenticationStrategyInterface> $strategies */
    public function __construct(
        private readonly iterable $strategies
    ) {}

    public function authenticate(Request $request, AuthProvider $provider): ?User
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($provider)) {
                return $strategy->authenticate($request);
            }
        }

        throw new RuntimeException('No authentication strategy found for: ' . $provider);
    }
}
