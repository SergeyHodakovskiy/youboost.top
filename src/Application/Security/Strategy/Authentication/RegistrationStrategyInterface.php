<?php

declare(strict_types=1);

namespace App\Application\Security\Strategy\Authentication;

use Symfony\Component\HttpFoundation\Request;
use App\Domain\User\Entity\User;

interface RegistrationStrategyInterface
{
    public function supports(string $provider): bool;

    public function register(Request $request): ?User;
}
