<?php

namespace App\Application\Security\Service;

use App\Domain\User\Entity\User;
use App\Application\Security\Provider;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Security\Strategy\AuthenticationStrategyInterface;

class AuthenticationService
{
    /** @var iterable|AuthenticationStrategyInterface[] */
    private iterable $strategies;

    private Provider $provider;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function authenticate(Request $request): ?User
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($request)) {

                return $strategy->authenticate($request);
            }
        }

        return null; // Или можно бросить исключение, если ни одна стратегия не подошла
    }

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }
}
