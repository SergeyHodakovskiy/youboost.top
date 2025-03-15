<?php

declare(strict_types=1);

namespace App\Application\Security\Strategy\Authentication;

use Symfony\Component\HttpFoundation\Request;
use App\Domain\User\Entity\User;
use App\Application\Security\AuthProvider;

interface AuthenticationStrategyInterface
{
    public function supports(AuthProvider $provider): bool;

    public function authenticate(Request $request): ?User;
}
