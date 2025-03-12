<?php

namespace App\Application\Security\Strategy;

use App\Application\Security\Provider;
use App\Domain\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRegistrationStrategy implements RegistrationStrategyInterface
{
    abstract protected function getProvider(): Provider;

    public function supports(Request $request): bool
    {
        return $request->request->get('provider') === $this->getProvider();
    }

    abstract public function register(Request $request): ?User;
}
